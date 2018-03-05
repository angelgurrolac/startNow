<?php

namespace startnow\Http\Controllers;


use Illuminate\Http\Request;
use startnow\Http\Requests;
use startnow\Http\Controllers\Controller;
use startnow\Miembros;
use DB;
use Input;
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
        $proyectos = DB::table('proyectos')->orderBy('created_at', 'desc')->value('idProyecto');
        $miembros = DB::table('miembrosequipo')
            ->where('idProyecto', '=', $proyectos)
            ->get();
      
      return view('proyectos.miembros', compact('proyectos', 'miembros'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $proyectos = DB::table('proyectos')->select('idProyecto')->orderBy('created_at', 'desc')->first();

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        #array('field1' => 'value 1', 'field2' => 'value2', ...),
        $input = $request->all();
        $condition = $input['nombres'];
        foreach ($condition as $key => $condition) {
            $miembro = new miembros;
            $miembro->nombres = $input['nombres'][$key];
            $miembro->apellidoP = $input['apellidoP'][$key];
            $miembro->apellidoM = $input['apellidoM'][$key];
            $miembro->urlPerfil = $input['urlPerfil'][$key];
            $miembro->idProyecto = $input['idProyecto'][$key];
            $miembro->imagenUrl = $input['idProyecto'][$key];
            $miembro->puesto = $input['puesto'][$key];
            $miembro->descripcion = $input['descripcion'][$key];

            $miembro->save();
        }
        #Miembros::insert($values);
        
        DB::table('miembrosequipo'); 
        
        return Redirect::to('miembros');
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
