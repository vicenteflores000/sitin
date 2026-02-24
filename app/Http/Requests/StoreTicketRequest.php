<?php

namespace App\Http\Requests;

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
            'tipo' => 'required|in:Soporte,Administrativo,Mejora',
            'categoria' => 'required|in:Computador,Impresora,Internet,Sistema,Correo,Telefonia,Otro',
            'impacto' => 'nullable|in:No impide trabajar,Dificulta el trabajo,Impide atender usuarios',
            'descripcion' => 'required|string|max:300',
            'locacion_id' => 'required|exists:locaciones,id',
        ];

        if (!auth()->check()) {
            $rules['auth_email'] = 'required|email';
            $rules['auth_password'] = 'required|string';
        }

        return $rules;
    }
}
