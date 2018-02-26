<?php

namespace startnow\Http\Controllers;


use Illuminate\Http\Request;
use startnow\Http\Requests;
use startnow\Http\Controllers\Controller;
use startnow\miembros;
use Session;
use Redirect;

class MiembrosController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('miembros.create');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('miembros.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        miembros::create([
            'nombres' => $request['nombres'],
            'apellido_P' => $request['apellido_P'],
            'apellido_M' => $request['apellido_M'],
            'urlPerfil' => $request['urlPerfil'],
            'idProyecto' => $request['idProyecto'],
			'imagenUrl' => $request['imagenUrl'],
            'puesto' => $request['puesto'],
            'descripcion' => $request['descripcion'],
            


        ]);
        
        return redirect('/')->with('message','store');
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
        $miembros = miembros::find($id);
        return view('miembros.edit',['miembro'=>$miembros]);
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
        $miembros = User::find($id);
        $miembros->fill($request->all());
        $miembros->save();
        Session::flash('message','Usuario Actualizado Correctamente');
        return Redirect::to('/miembros');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        miembros::destroy($id);
        Session::flash('message','Usuario Eliminado Correctamente');
        return Redirect::to('/miembros');
    }


}
