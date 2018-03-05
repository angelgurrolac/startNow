<?php

namespace startnow\Http\Controllers;
use startnow\competencias;
use Illuminate\Http\Request;
use DB;
use Input;
use Redirect;


class CompetenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {
        
        $proyectos = DB::table('proyectos')->orderBy('created_at','desc')->value('idProyecto');
        $competencias = DB::table('competencias')
            ->where('idProyecto', '=', $proyectos)
            ->get();
      
      return view('proyectos.competencias', compact('proyectos', 'competencias'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $input = $request->all();
        $condition = $input['nombreCompetencia'];
        foreach ($condition as $key => $condition) {
            $competencia = new competencias;

            $competencia->nombreCompetencia = $input['nombreCompetencia'][$key];
            $competencia->descripcionCompetencia = $input['descripcionCompetencia'][$key];
            $competencia->urlImagenCompetencia = $input['urlImagenCompetencia'][$key];
            $competencia->idProyecto = $input['idProyecto'][$key];
       
   
            $competencia->save();
        }
        #Miembros::insert($values);
         
        DB::table('competencias'); 
        
        return Redirect::to('competencias');
         
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         
       //$miembrosequipo = miembrosequipo::select()->where('idMiembro',$id)->get();
       
        //dd($url);
      // return view('informacion',['miembrosequipo'=>$miembrosequipo]);

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
