<?php

namespace startnow\Http\Controllers;
use startnow\proyectos;
use startnow\equipo;
use startnow\Youtube;
use Illuminate\Http\Request;

class prueba extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$id = $request->input('id');
        $proyectos = proyectos::select('nombre')->where('idProyecto',1)->get();
        return view('informacion',['proyectos'=>$proyectos]);
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
        $miembrosEquipo = equipo::select()->where('idMiembro',$proyectos[0]->idMiembro)->get();
        $url="http://youtube.com/embed/".Youtube::parseVIdFromURL($proyectos[0]->videoUrl)."";
        //dd($url);
       return view('informacion',['proyectos'=>$proyectos,'URL'=>$url,'miembro'=>$miembrosEquipo ]);

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
