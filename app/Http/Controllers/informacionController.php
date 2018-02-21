<?php

namespace startnow\Http\Controllers;
use startnow\proyectos;
use startnow\informacion;
use Illuminate\Http\Request;

class informacionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */



    public function index(Request $request){
        $id = $request->input('id');
        $proyectos = proyectos::select('nombre')->where('idProyecto',$id)->get();
        $miembrosequipo = miembrosequipo::select('nombres','apellidoP','apellidoM')->where('idMiembro',1)->get();
        dd($miembrosequipo);
        //return view('informacion',['proyectos'=>$proyectos,'miembrosequipo'=>   $miembrosequipo[0]]);
    }    
    
}
