<?php

namespace App\Http\Controllers;

use App\Models\AllowedDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AllowedDomainController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = strtolower(trim($data['domain']));
        $domain = ltrim($domain, '@');

        if ($domain === '' || ! str_contains($domain, '.')) {
            return back()->withErrors(['domain' => 'Dominio inválido.']);
        }

        AllowedDomain::firstOrCreate(['domain' => $domain]);

        return back()->with('success', 'Dominio agregado.');
    }

    public function destroy(AllowedDomain $domain): RedirectResponse
    {
        $domain->delete();

        return back()->with('success', 'Dominio eliminado.');
    }
}
