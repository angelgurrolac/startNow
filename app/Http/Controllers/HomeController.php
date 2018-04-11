<?php

namespace startnow\Http\Controllers;
use startnow\proyectos;
use startnow\user;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        #$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $proyectos = proyectos::limit(3)->orderBy('created_at','desc')->get();
         $randoms = proyectos::limit(3)->inRandomOrder()->get();
         $apoyando = DB::table('users')->count();
         $apoyados = DB::table('proyectos')->count();
         $finalizados = proyectos::where('estatus', '=', 'completo')->count();

        return view('welcome',['proyectos'=>$proyectos,'randoms' => $randoms , 'apoyando' => $apoyando, 'apoyados' => $apoyados, 'finalizados' => $finalizados]);
    }
}

