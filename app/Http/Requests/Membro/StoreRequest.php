<?php

namespace App\Http\Requests\Membro;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|string|max:50',
            'email' => 'nullable|email|max:50',
            'telefone' => 'required|string|max:15',
            'sexo' => 'required|string|size:1',
            'data_nascimento' => 'nullable|date',
            'comuna_id' => 'required|exists:comunas,id',
            'bi' => 'required|string',
            'pai' => 'nullable|string',
            'mae' => 'nullable|string',
            'estado_militante' => 'nullable|string', // Verificar os possíveis estados
            'ano_ingresso' => 'nullable|integer',
            'onde_ingressou' => 'nullable|string',
            'numero_membro' => 'nullable|string',
            'cartao_municipe' => 'nullable|string',
            'comite_id' => 'required|exists:comites,id',
            'orgaos' => 'nullable|array'
        ];
    }


}
