<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\AllowedDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserCreatedCredentials;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profiles.index', [
            'users' => User::orderBy('name')->get(),
            'allowedDomains' => AllowedDomain::orderBy('domain')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.profiles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (! AllowedDomain::allowsEmail($value)) {
                        $fail('Dominio no permitido.');
                    }
                },
            ],
            'role' => 'required|in:admin,user',
            'active' => 'boolean',
        ]);

        $plainPassword = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'must_change_password' => true,
            'glpi_user_id' => null,
            'role' => $request->role,
            'active' => $request->boolean('active', true),
        ]);

        try {
            Mail::to($user->email)->send(new UserCreatedCredentials($user, $plainPassword));
        } catch (\Throwable $exception) {
            return redirect()
                ->route('admin.profiles.index')
                ->with('success', 'Usuario creado. No se pudo enviar el correo con la clave.');
        }

        return redirect()
            ->route('admin.profiles.index')
            ->with('success', 'Usuario creado. Se envió una clave al correo.');
    }

    public function edit(User $user)
    {
        return view('admin.profiles.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Evitar que el admin se quite su propio rol
        if (
            auth()->id() === $user->id &&
            $request->role !== 'admin'
        ) {
            abort(403, 'No puedes quitarte permisos de administrador');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $user->id,
                function ($attribute, $value, $fail) {
                    if (! AllowedDomain::allowsEmail($value)) {
                        $fail('Dominio no permitido.');
                    }
                },
            ],
            'password' => 'nullable|min:8',
            'role' => 'required|in:admin,user',
            'active' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'active' => $request->boolean('active'),
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        return redirect()
            ->route('admin.profiles.index')
            ->with('success', 'Perfil actualizado');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
