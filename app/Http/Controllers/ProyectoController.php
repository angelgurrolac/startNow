<?php

namespace startnow\Http\Controllers;

use startnow\Http\Controllers\Controller;
use Illuminate\Http\Request;
use startnow\Http\Requests;
use startnow\Http\Requests\ProyectoCreateRequest;
use startnow\Http\Requests\ProyectoUpdateRequest;
use Redirect;
use Session;
use startnow\proyectos;
use Illuminate\Routing\Route;
use Auth;
use startnow\miembros;
use startnow\categorias;

class ProyectoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        
     }

    public function find(Route $route){
        $this->proyecto = proyectos::find($route->getParameter('proyecto'));
    }
    public function index()
    {
        if (Auth::guest()) {
            return redirect('/');
        }

        $user = Auth::user()->id;
        $proyectos= \startnow\proyectos::where('idUsuario', '=', $user)->get();
        return view ('proyectos.index',compact('proyectos'));
    }

    protected function validator(array $data)
    {
        
       
        return Validator::make($data, [
            'nombre' => 'required|unique:proyectos|string|max:50',
            'descCorta' => 'required|string|max:290',
            'descLarga' => 'required|string|max:1000',
            'videoUrl' => 'required|string|max:150',
            'metaMin' => 'required|string|max:15',
            'metaMax' => 'required|string|max:15',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|',
            'idUsuario' => 'required|numeric|max:10',
            'numeroClientes' => 'required|numeric|max:10',
            'inversion' => 'required|string|max:7',
            'valorMercado' => 'required|string|max:290',
            'descComollegarClientes' => 'required|string|max:1000',
            'propuestaValor' => 'required|string|max:500',
        ]);
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $miembros = [];
        $competencias = [];
        $categorias = categorias::all();
        $selectCategorias = array();

        foreach($categorias as $categoria) {
            $selectCategorias[$categoria->id] = $categoria->name;
        }
        return view('proyectos.create', ['miembros' => $miembros, 'competencias' => $competencias, 'categorias' => $selectCategorias]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    #$proyecto = []
    public function store(Request $request)
    {
         $user = Auth::user()->id;
        $proyecto = [
            'nombre' => $request['nombre'],
            'descCorta' => $request['descCorta'],
            'descLarga' => $request['descLarga'],
            'imagenUrl' => $request['imagenUrl'],
            'videoUrl' => $request['videoUrl'],
            'metaMin' => $request['metaMin'],
            'metaMax' => $request['metaMax'],
            'fechaInicio' => $request['fechaInicio'],
            'fechaFin' => $request['fechaFin'],
            'numeroClientes' => $request['numeroClientes'],
            'inversion' => $request['inversion'],
            'valorMercado' => $request['valorMercado'],
            'descComollegarClientes' => $request['descComollegarClientes'],
            'propuestaValor' => $request['propuestaValor'],
            'estatus' => 'progreso',
            'idUsuario' => $user,
            'categoria' => $request['categoria'],
        ];
        \startnow\proyectos::create($proyecto);

        $last = proyectos::latest()->first();
        $input = $request->all();
        $miembros=[];
        $condition = $input['nombres'];
        foreach ($condition as $key => $condition) {
            $miembro = new miembros;
            $miembro->nombres = $input['nombres'][$key];
            $miembro->apellidoP = $input['apellidoP'][$key];
            $miembro->apellidoM = $input['apellidoM'][$key];
            $miembro->urlPerfil = $input['urlPerfil'][$key];
            $miembro->idProyecto = $last->idProyecto;
            $miembro->imagenMiembro = $input['imagenMiembro'][$key];
            $miembro->puesto = $input['puesto'][$key];
            $miembro->descripcion = $input['descripcion'][$key];
            
            $miembro->save();
        }
        // \startnow\proyectos::create([

        //     'nombre' => $request['nombre'],
        //     'descCorta' => $request['descCorta'],
        //     'descLarga' => $request['descLarga'],
        //     'imagenUrl' => $request['imagenUrl'],
        //     'videoUrl' => $request['videoUrl'],
        //     'metaMin' => $request['metaMin'],
        //     'metaMax' => $request['metaMax'],
        //     'fechaInicio' => $request['fechaInicio'],
        //     'fechaFin' => $request['fechaFin'],
        //     'numeroClientes' => $request['numeroClientes'],
        //     'inversion' => $request['inversion'],
        //     'valorMercado' => $request['valorMercado'],
        //     'descComollegarClientes' => $request['descComollegarClientes'],
        //     'propuestaValor' => $request['propuestaValor'],
        //     'estatus' => 'progreso',

        // ]);
        return Redirect::to('proyectos');
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
    public function update(Request $request, $id)
    {
        $proyecto = proyectos::find($id);
        $proyecto->fill($request->all());
        $proyecto->save();
        Session::flash('message','Proyecto Actualizado Correctamente');
        return Redirect::to('proyectos');
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
