<?php

namespace startnow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use startnow\Requests\Requests;
class UserUpdateRequest extends FormRequest
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
            'email' => 'required|string|max:290',
            'password' => 'required|string|max:15',
            'Apeido_P' => 'required|string|max:50',
            'Apeido_M' => 'required|string|max:50',
            'Direccion' => 'required|string|max:300',
            'CP' => 'required|numeric',
            'Pais' => 'required|string',
            'CD' => 'required|string',
            'Numero_Ext' => 'required|numeric',
            'Numero_Cel' => 'required|numeric',
            'Numero_Casa' => 'required|numeric',
            'Sex' => 'required|string|max:10',
            'Fecha' => 'required|date',
            'Perfil' => 'required|string|max:100',
        ];
    }
}
