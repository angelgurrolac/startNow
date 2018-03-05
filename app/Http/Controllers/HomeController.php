<?php

namespace startnow\Http\Controllers;
use startnow\proyectos;
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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $proyectos = proyectos::limit(6)->orderBy('created_at','desc')->get(); 

        return view('welcome',['proyectos'=>$proyectos]);
    }
}

