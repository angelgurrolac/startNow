<?php

namespace startnow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use startnow\Requests\Requests;

class UserCreateRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'email' => 'required|unique:users|string|max:290',
            'password' => 'required|string|max:15',
            'Apeido_P' => 'required|string|max:50',
            'Apeido_M' => 'required|string|max:50',
            'metaMin' => 'required|money_format|max:7',
            'metaMax' => 'required|numeric|max:7',
            'Direccion' => 'required|string|max:300',
            'CP' => 'required|numeric|max:10',
            'Pais' => 'required|string|max:100',
            'Numero_Ext' => 'required|numeric|max:10',
            'Numero_Cel' => 'required|numeric|max:10',
            'Numero_Casa' => 'required|numeric|max:10',
            'Sex' => 'required|string|max:10',
            'Fecha' => 'required|date(format)|max:10',
            'Perfil' => 'required|string|max:100',
        ];
    }
}
