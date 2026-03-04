<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Services\IaSuggestionService;
use App\Services\PriorityService;
use App\Services\GlpiService;
use App\Models\Ticket;
use App\Models\Locacion;
use App\Models\AllowedDomain;
use App\Models\TicketStatusEvent;
use App\Mail\TicketCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function create()
    {
        $locaciones = Locacion::with('padre')
            ->whereNotNull('locacion_padre_id')
            ->orderBy('nombre')
            ->get();

        return view('ticket.create', compact('locaciones'));
    }

    public function store(StoreTicketRequest $request)
    {
        if (!auth()->check()) {
            $credentials = [
                'email' => $request->input('auth_email'),
                'password' => $request->input('auth_password'),
            ];

            $throttleKey = $this->throttleKey($credentials['email'] ?? '', $request->ip());
            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                return $this->authErrorResponse($request, 'Demasiados intentos. Intenta en unos minutos.', 429, [
                    'seconds' => $seconds,
                ]);
            }

            $validator = validator($credentials, [
                'email' => [
                    'required',
                    'string',
                    'email',
                'max:255',
                function ($attribute, $value, $fail) {
                        if (! AllowedDomain::allowsEmail($value)) {
                            $fail('Debe usar un correo institucional.');
                        }
                    },
                ],
                'password' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return $this->authErrorResponse($request, $validator->errors()->first(), 422);
            }

            if (!Auth::attempt($credentials)) {
                RateLimiter::hit($throttleKey);
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Correo o clave inválidos.',
                    ], 422);
                }

                return back()
                    ->withErrors(['auth_email' => 'Correo o clave inválidos.'])
                    ->withInput($request->except('auth_password'));
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
        }

        $user = auth()->user();
        if ($user && ! $user->isActive()) {
            auth()->logout();
            return $this->authErrorResponse($request, 'Usuario desactivado.', 403);
        }
        if ($user?->must_change_password) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Debes cambiar tu clave provisoria antes de enviar un ticket.',
                    'redirect' => route('password.force.show'),
                ], 423);
            }

            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('password.force.show');
        }

        $ticket = Ticket::create([
            'tipo' => $request->tipo,
            'area' => $request->input('area') ?: 'No especificado',
            'categoria' => $request->categoria,
            'impacto' => $request->input('impacto') ?: 'No especificado',
            'descripcion' => trim($request->descripcion),
            'usuario_mail' => $user->email,
            'locacion_id' => $request->locacion_id,
            'glpi_location_id' => $request->input('glpi_location_id'),
            'estado_glpi' => null,

            // datos ocultos
            'pc' => gethostname(),
            'usuario' => $_SERVER['USERNAME'] ?? null,
            'ip_origen' => $request->ip(),
            'origen' => 'Formulario TI',
            'estado_envio_glpi' => null,
        ]);

        TicketStatusEvent::create([
            'ticket_id' => $ticket->id,
            'from_status' => null,
            'to_status' => 'nuevo',
            'started_at' => now(),
            'changed_by' => $user->id,
        ]);
        $ticket->load('locacion.padre');

        $priorityData = app(PriorityService::class)->calculate($ticket);
        $ticket->update($priorityData);
        $iaData = app(IaSuggestionService::class)->analyze($ticket->descripcion);
        $ticket->update($iaData);
        $glpiReady = $user->glpi_user_id
            && $ticket->glpi_location_id
            && config('services.glpi.url')
            && config('services.glpi.app_token')
            && config('services.glpi.user_token');

        if ($glpiReady) {
            $ticket->update([
                'estado_glpi' => 'Enviado',
                'estado_envio_glpi' => 'pendiente',
            ]);
            app(GlpiService::class)->createTicket($ticket);
        }

        try {
            Mail::to('informatica@mdonihue.cl')
                ->cc($user->email)
                ->send(new TicketCreated($ticket));
        } catch (\Throwable $exception) {
            Log::warning('No se pudo enviar correo de ticket', [
                'ticket_id' => $ticket->id,
                'error' => $exception->getMessage(),
            ]);
        }


        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'ticket_id' => $ticket->id,
            ]);
        }

        return redirect()
            ->route('ticket.create')
            ->with('success', "Ticket enviado correctamente con el ID: {$ticket->id}");
    }

    protected function throttleKey(string $email, string $ip): string
    {
        return Str::transliterate(Str::lower($email) . '|' . $ip);
    }

    protected function authErrorResponse(StoreTicketRequest $request, string $message, int $status = 422, array $meta = [])
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                ...$meta,
            ], $status);
        }

        return back()
            ->withErrors(['auth_email' => $message])
            ->withInput($request->except('auth_password'));
    }
}
