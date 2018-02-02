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

 
    // public function index($id)
    // {
        
    //      //$informacion = informacion::informacion();
        
    //     $proyectos = proyectos::limit(1)->get(); 
    //     return view('informacion',['proyectos'=>$proyectos]);

    // }

    public function index(Request $request){
        $id = $request->input('id');
        $proyectos = proyectos::select('nombre')->where('idProyecto',$id)->get();
        return view('informacion',['proyectos'=>$proyectos]);
    }    
    
}
