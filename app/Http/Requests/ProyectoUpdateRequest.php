<?php

namespace startnow\Http\Requests;
use startnow\Requests\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProyectoUpdateRequest extends FormRequest
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
            'nombre' => 'required|unique:proyectos|string|max:50',
            'descCorta' => 'required|string|max:290',
            'descLarga' => 'required|string|max:1000',
            'imagenUrl' => 'required|string|max:150',
            'videoUrl' => 'required|string|max:150',
            'metaMin' => 'required|string',
            'metaMax' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date',
            'idProducto' => 'required|numeric',
            'idMercado' => 'required|numeric',
            'idUsuario' => 'required|numeric',
            'numeroClientes' => 'required|numeric',
            'inversion' => 'required|string',
            'valorMercado' => 'required|string|max:290',
            'descComollegarClientes' => 'required|string|max:1000',
            'propuestaValor' => 'required|string|max:500',
            'idMiembro' => 'required|numeric',
        ];
    }
}
