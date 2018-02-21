<?php

namespace startnow\Http\Controllers;

use Illuminate\Http\Request;
use startnow\proyectos;
use startnow\equipo;
use startnow\competencias;
use startnow\mercados;
use startnow\Youtube;
use startnow\productos;
use startnow\etapas;
use startnow\alianzas;

class TodosController extends Controller
{
   public function index()
    {

    	$proyectos = proyectos::all();



        
       

       #         $proyectos = proyectos::select('nombre')->where('idProyecto',1)->get();
        return view('todos',['proyectos'=>$proyectos]);
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
         
    }
 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

         
        $proyectos = proyectos::select()->where('idProyecto',$id)->get();

     


        $url="http://youtube.com/embed/".Youtube::parseVIdFromURL($proyectos[0]->videoUrl)."";
        //dd($miembrosequipo);
       return view('todos',['proyecto'=>$proyectos[0],'URL'=>$url,'miembrosequipo'=>$miembrosequipo,'competencias'=>$competencias,'mercados'=>$mercados,'productos'=>$productos,'etapas'=>$etapas,'alianzas'=>$alianzas]);


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
