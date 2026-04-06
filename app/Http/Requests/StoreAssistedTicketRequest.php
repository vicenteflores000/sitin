<?php

namespace App\Http\Requests;

use App\Models\Locacion;
use App\Models\AllowedDomain;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssistedTicketRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()?->isAdmin();
    }

    public function rules()
    {
        $rules = [
            'assisted_user_name' => 'required|string|max:100',
            'assisted_user_email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (! AllowedDomain::allowsEmail($value)) {
                        $fail('El correo del funcionario no pertenece a un dominio permitido.');
                    }
                },
            ],
            'assisted_channel' => 'required|in:llamada,whatsapp,presencial,correo,otro',
            'assisted_reason' => 'required|string|max:255',
            'tipo' => 'required|in:Algo no funciona...,Necesito ayuda para algo...,No puedo acceder / entrar ...,Necesito una mejora / cambio en algo...',
            'impacto' => 'nullable|in:No impide trabajar,Dificulta trabajar,Impide trabajar',
            'descripcion' => 'required|string|max:300',
            'locacion_id' => [
                'required',
                'exists:locaciones,id',
                function ($attribute, $value, $fail) {
                    $email = $this->input('assisted_user_email', '');
                    $domain = strtolower(trim(substr(strrchr($email ?? '', '@'), 1) ?: ''));
                    if ($domain === '') {
                        $fail('No se pudo validar el dominio del funcionario.');
                        return;
                    }

                    $allowed = Locacion::whereKey($value)
                        ->whereHas('allowedDomains', function ($query) use ($domain) {
                            $query->where('domain', $domain);
                        })
                        ->exists();

                    if (! $allowed) {
                        $fail('La locación seleccionada no pertenece al dominio del funcionario.');
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
