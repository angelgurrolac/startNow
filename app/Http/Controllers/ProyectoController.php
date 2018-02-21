<?php

namespace startnow\Http\Controllers;

use Illuminate\Http\Request;
use startnow\Http\Requests;
use startnow\Http\Requests\ProyectoCreateRequest;
use startnow\Http\Requests\ProyectoUpdateRequest;
use Redirect;
use Session;
use startnow\proyectos;
use Illuminate\Routing\Route;

use startnow\Http\Controllers\Controller;



class ProyectoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function find(Route $route){
        $this->proyecto = proyectos::find($route->getParameter('proyecto'));
    }
    public function index()
    {
        $proyectos= \startnow\proyectos::paginate(5);
        return view ('proyectos.index',compact('proyectos'));
    }

    protected function validator(array $data)
    {
        
       
        return Validator::make($data, [
            'nombre' => 'required|unique:proyectos|string|max:50',
            'descCorta' => 'required|string|max:290',
            'descLarga' => 'required|string|max:1000',
            'imagenUrl' => 'required|string|max:150',
            'videoUrl' => 'required|string|max:150',
            'metaMin' => 'required|money_format|max:7',
            'metaMax' => 'required|numeric|max:7',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|',
            'idProducto' => 'required|numeric|max:10',
            'idMercado' => 'required|numeric|max:10',
            'idUsuario' => 'required|numeric|max:10',
            'numeroClientes' => 'required|numeric|max:10',
            'inversion' => 'required|string|max:7',
            'valorMercado' => 'required|string|max:290',
            'descComollegarClientes' => 'required|string|max:1000',
            'propuestaValor' => 'required|string|max:500',
            'idMiembro' => 'required|numeric|max:10',
        ]);
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
    public function store(ProyectoCreateRequest $request)
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
      
        $proyecto = proyectos::find($id);
        return view('proyectos.edit',['proyecto'=>$proyecto]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProyectoUpdateRequest $request, $id)
    {
        $proyecto = proyectos::find($id);
        $proyecto->fill($request->all());
        $proyecto->save();
        Session::flash('message','Proyecto Actualizado Correctamente');
        return Redirect::to('/proyectos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        proyectos::destroy($id);
        Session::flash('message','Proyectos Eliminado Correctamente');
        return Redirect::to('/proyectos');
    }
}
