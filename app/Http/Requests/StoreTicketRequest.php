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
        return [
            'tipo' => 'required|in:Soporte,Administrativo,Mejora',
            'area' => 'required|in:CESFAM,Urgencia,Registro Civil,Administracion',
            'categoria' => 'required|in:Computador,Impresora,Internet,Sistema,Correo,Telefonia,Otro',
            'impacto' => 'required|in:No impide trabajar,Dificulta el trabajo,Impide atender usuarios',
            'descripcion' => 'required|string|max:300',
        ];
    }
}
