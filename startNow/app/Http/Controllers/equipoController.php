<?php

namespace startnow\Http\Controllers;
use startnow\equipo;
use startnow\Youtube;
use Illuminate\Http\Request;

class equipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$id = $request->input('id');
        $miembrosEquipo = miembrosEquipo::select('nombres')->where('idMiembro',1)->get();
        return view('informacion',['miembrosEquipo'=>$miembrosEquipo]);
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
         
       $miembrosEquipo = miembrosEquipo::select()->where('idMiembro',$id)->get();
        //$url="http://youtube.com/embed/".Youtube::parseVIdFromURL($proyectos[0]->videoUrl)."";
        //dd($url);
       return view('informacion',['miembrosEquipo'=>$miembrosEquipo]);

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
