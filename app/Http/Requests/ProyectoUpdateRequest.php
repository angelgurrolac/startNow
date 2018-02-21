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
            'metaMin' => 'required|money_format|max:7',
            'metaMax' => 'required|numeric|max:7',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date',
            'idProducto' => 'required|numeric|max:10',
            'idMercado' => 'required|numeric|max:10',
            'idUsuario' => 'required|numeric|max:10',
            'numeroClientes' => 'required|numeric|max:10',
            'inversion' => 'required|string|max:7',
            'valorMercado' => 'required|string|max:290',
            'descComollegarClientes' => 'required|string|max:1000',
            'propuestaValor' => 'required|string|max:500',
            'idMiembro' => 'required|numeric|max:10',
        ];
    }
}
