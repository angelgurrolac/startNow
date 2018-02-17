<?php

namespace startnow\Http\Controllers;

use Illuminate\Http\Request;
use startnow\Http\Requests;
use Redirect;
use Session;
use startnow\proyectos;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $proyectos= \startnow\proyectos::paginate(2);
        return view ('proyectos.create',compact('proyectos'));
    }



 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('proyectos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \startnow\proyectos::create([

            'nombre' => $request['nombre'],
            'descCorta' => $request['descCorta'],
            'descLarga' => $request['descLarga'],
            'imagenUrl' => $request['imagenUrl'],
            'videoUrl' => $request['videoUrl'],
            'metaMin' => $request['metaMin'],
            'metaMax' => $request['metaMax'],
            'fechaInicio' => $request['fechaInicio'],
            'fechaFin' => $request['fechaFin'],
            'idProducto' => '1',
            'idUsuario' => '1',
            'idMercado' => '1',
            'numeroClientes' => $request['numeroClientes'],
            'inversion' => $request['inversion'],
            'valorMercado' => $request['valorMercado'],
            'descComollegarClientes' => $request['descComollegarClientes'],
            'propuestaValor' => $request['propuestaValor'],
            'idMiembro' => '1',

        ]);
        return "Proyecto registrado";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
