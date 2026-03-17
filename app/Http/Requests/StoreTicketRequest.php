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
            'tipo' => 'required|in:Algo no funciona...,Necesito ayuda para algo...,No puedo acceder / entrar ...,Necesito una mejora / cambio en algo...',
            'impacto' => 'nullable|in:No impide trabajar,Dificulta trabajar,Impide trabajar',
            'descripcion' => 'required|string|max:300',
            'locacion_id' => 'required|exists:locaciones,id',
            'locacion_hija_texto' => 'required|string|max:255',
        ];

        return $rules;
    }
}
