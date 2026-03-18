<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Actions\SyncTicketStatusFromGlpi;
use Illuminate\Http\RedirectResponse;
use App\Actions\ReenviarTicketsPendientesAGlpi;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->renderDashboard('dashboard');
    }

    public function admin()
    {
        $calendarTickets = Ticket::with('locacion.padre', 'currentAssignment.technician')
            ->orderByDesc('created_at')
            ->get();

        $adminTickets = Ticket::with([
            'locacion.padre',
            'latestStatusEvent',
            'currentAssignment.technician',
            'resolution',
            'parts',
            'actions.creator',
            'attachments',
        ])->orderByDesc('created_at')->get();

        $admins = User::where('role', 'admin')->orderBy('name')->get();

        $domainCards = [
            'salud' => [
                'label' => 'Salud',
                'style' => 'background-color: #2F7FA3;',
                'text' => 'text-white',
            ],
            'educacion' => [
                'label' => 'Educación',
                'style' => 'background-color: #B53A3A;',
                'text' => 'text-white',
            ],
            'municipal' => [
                'label' => 'Municipal',
                'style' => 'background-color: #2E7A57;',
                'text' => 'text-white',
            ],
        ];

        $domainMap = [
            'salud.mdonihue.cl' => 'salud',
            'edudonihue.cl' => 'educacion',
            'mdonihue.cl' => 'municipal',
        ];

        $domainStats = [];
        foreach ($domainCards as $key => $card) {
            $domainStats[$key] = [
                'total' => 0,
                'nuevo' => 0,
                'asignado' => 0,
                'resuelto' => 0,
            ];
        }

        $totalStats = [
            'total' => 0,
            'nuevo' => 0,
            'asignado' => 0,
            'resuelto' => 0,
        ];

        $newStatuses = ['nuevo', 'recibido'];
        $assignedStatuses = ['asignado', 'en_progreso', 'en_proceso', 'standby', 'en_espera'];
        $resolvedStatuses = ['resuelto', 'cerrado'];

        $normalizeDomain = function (?string $value): string {
            if (!$value) {
                return '';
            }
            return Str::of($value)
                ->lower()
                ->replace('@', '')
                ->replace(' ', '')
                ->ascii()
                ->__toString();
        };

        $resolveDomainKeyFromEmail = function (?string $email) use ($domainMap, $normalizeDomain): ?string {
            if (! $email || ! str_contains($email, '@')) {
                return null;
            }
            $domain = $normalizeDomain(substr(strrchr($email, '@'), 1) ?: '');
            return $domainMap[$domain] ?? null;
        };

        foreach ($adminTickets as $ticket) {
            $rawStatus = $ticket->latestStatusEvent?->to_status ?? $ticket->estado_glpi ?? 'nuevo';
            $status = Str::of($rawStatus)->lower()->__toString();

            $totalStats['total']++;
            if (in_array($status, $newStatuses, true)) {
                $totalStats['nuevo']++;
            }
            if (in_array($status, $assignedStatuses, true)) {
                $totalStats['asignado']++;
            }
            if (in_array($status, $resolvedStatuses, true)) {
                $totalStats['resuelto']++;
            }

            $ticketDomainKey = $resolveDomainKeyFromEmail($ticket->usuario_mail);
            $ticket->domain_keys = $ticketDomainKey ? [$ticketDomainKey] : [];
            if ($ticketDomainKey) {
                $key = $ticketDomainKey;
                if (!array_key_exists($key, $domainStats)) {
                    continue;
                }
                $domainStats[$key]['total']++;
                if (in_array($status, $newStatuses, true)) {
                    $domainStats[$key]['nuevo']++;
                }
                if (in_array($status, $assignedStatuses, true)) {
                    $domainStats[$key]['asignado']++;
                }
                if (in_array($status, $resolvedStatuses, true)) {
                    $domainStats[$key]['resuelto']++;
                }
            }
        }

        return view('dashboard-admin', compact(
            'calendarTickets',
            'adminTickets',
            'admins',
            'domainCards',
            'domainStats',
            'totalStats'
        ));
    }

    protected function renderDashboard(string $view)
    {
        $userMail = auth()->user()->email;

        $tickets = Ticket::where('usuario_mail', $userMail)
            ->with([
                'assignments.technician',
                'schedules',
                'actions.creator',
                'resolution',
                'latestStatusEvent',
                'attachments',
            ])
            ->orderByDesc('created_at')
            ->get();

        $total = $tickets->count();

        $abiertos = $tickets->whereIn('estado_glpi', [
            'recibido',
            'en_proceso',
            'en_espera',
        ])->count();

        $cerrados = $tickets->where('estado_glpi', 'cerrado')->count();

        $ultimos = $tickets;

            $glpiStatus = 'online';

        return view($view, compact(
            'total',
            'abiertos',
            'cerrados',
            'ultimos',
            'glpiStatus'
        ));
    }

    public function reenviarPendientes(
        ReenviarTicketsPendientesAGlpi $action
    ) {
        $result = $action->execute();

        return redirect()
            ->route('dashboard')
            ->with('reenviar_result', $result);
    }

    public function syncEstados(
        SyncTicketStatusFromGlpi $sync
    ): RedirectResponse {
        $result = $sync->execute();

        return redirect()
            ->route('dashboard')
            ->with('sync_result', $result);
    }
}
