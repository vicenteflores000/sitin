<?php

namespace App\Http\Requests;

use App\Models\Locacion;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'tipo' => 'required|in:Algo no funciona...,Necesito ayuda para algo...,No puedo acceder / entrar ...,Necesito una mejora / cambio en algo...',
            'impacto' => 'nullable|in:No impide trabajar,Dificulta trabajar,Impide trabajar',
            'descripcion' => 'required|string|max:300',
            'locacion_id' => [
                'required',
                'exists:locaciones,id',
                function ($attribute, $value, $fail) {
                    $user = $this->user();
                    if (! $user) {
                        return;
                    }
                    $email = $user?->email ?? '';
                    $domain = strtolower(trim(substr(strrchr($email, '@'), 1) ?: ''));
                    if ($domain === '') {
                        $fail('No se pudo validar el dominio del usuario.');
                        return;
                    }

                    $allowed = Locacion::whereKey($value)
                        ->whereHas('allowedDomains', function ($query) use ($domain) {
                            $query->where('domain', $domain);
                        })
                        ->exists();

                    if (! $allowed) {
                        $fail('La locación seleccionada no pertenece a tu dominio.');
                    }
                },
            ],
            'locacion_hija_texto' => 'required|string|max:255',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:20480',
        ];

        return $rules;
    }
}
