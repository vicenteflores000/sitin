<?php

namespace App\Http\Controllers;

use App\Models\Locacion;
use Illuminate\Http\Request;

class LocacionController extends Controller
{
    public function index()
    {
        $establecimientos = Locacion::raiz()
            ->with('hijos')
            ->orderBy('nombre')
            ->get();

        return view('admin.locaciones.index', compact('establecimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'locacion_padre_id' => 'nullable|exists:locaciones,id',
        ]);

        Locacion::create([
            'nombre' => $request->nombre,
            'slug' => $request->slug,
            'locacion_padre_id' => $request->parent_id ?: null,
            'activo' => true,
        ]);

        return redirect()->back()->with('success', 'Locación creada correctamente');
    }
}
