<?php

namespace App\Http\Controllers;

use App\Models\Locacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocacionController extends Controller
{
    public function index()
    {
        $establecimientos = Locacion::raiz()
            ->with('hijos.funcionarios')
            ->orderBy('nombre')
            ->get();

        $funcionarios = User::with('locaciones')->orderBy('name')->get();

        return view('admin.locaciones.index', compact('establecimientos', 'funcionarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locaciones', 'slug')->where(function ($query) use ($request) {
                    $parentId = $request->input('locacion_padre_id');
                    return $parentId
                        ? $query->where('locacion_padre_id', $parentId)
                        : $query->whereNull('locacion_padre_id');
                }),
            ],
            'locacion_padre_id' => 'nullable|exists:locaciones,id',
        ]);

        Locacion::create([
            'nombre' => $request->nombre,
            'slug' => $request->slug,
            'locacion_padre_id' => $request->input('locacion_padre_id') ?: null,
            'activo' => true,
        ]);

        return redirect()->back()->with('success', 'Locación creada correctamente');
    }

    public function edit(Locacion $locacion)
    {
        return redirect()->route('admin.locaciones.index');
    }

    public function update(Request $request, Locacion $locacion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locaciones', 'slug')
                    ->ignore($locacion->id)
                    ->where(function ($query) use ($request) {
                        $parentId = $request->input('locacion_padre_id');
                        return $parentId
                            ? $query->where('locacion_padre_id', $parentId)
                            : $query->whereNull('locacion_padre_id');
                    }),
            ],
            'locacion_padre_id' => 'nullable|exists:locaciones,id',
        ]);

        $locacion->update([
            'nombre' => $request->nombre,
            'slug' => $request->slug,
            'locacion_padre_id' => $request->input('locacion_padre_id') ?: null,
        ]);

        return redirect()->back()->with('success', 'Locación actualizada correctamente');
    }

    public function destroy(Locacion $locacion)
    {
        if ($locacion->hijos()->exists()) {
            return redirect()->back()->withErrors(['locacion' => 'No puedes eliminar un establecimiento con locaciones hijas.']);
        }

        $locacion->delete();

        return redirect()->back()->with('success', 'Locación eliminada correctamente');
    }

    public function assignFuncionario(Request $request, Locacion $locacion)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->locaciones()->syncWithoutDetaching([$locacion->id]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Usuario asignado correctamente',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'locacion_ids' => $user->locaciones()->pluck('locaciones.id')->all(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Usuario asignado correctamente');
    }

    public function removeFuncionario(Locacion $locacion, User $user)
    {
        if (! $user->locaciones()->where('locaciones.id', $locacion->id)->exists()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'El funcionario no pertenece a esta locación.'], 422);
            }
            return redirect()->back()->withErrors(['user_id' => 'El funcionario no pertenece a esta locación.']);
        }

        $user->locaciones()->detach($locacion->id);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Usuario eliminado de la locación',
                'user_id' => $user->id,
            ]);
        }

        return redirect()->back()->with('success', 'Usuario eliminado de la locación');
    }
}
