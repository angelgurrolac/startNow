<?php

namespace startnow\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Session;
use Redirect;
use startnow\Http\Requests;
use startnow\Http\Requests\LoginRequest;
use startnow\Http\Controllers\Controller;


class LogController extends Controller
{
    public function index()
    {
        //
        $response = "";
        return view('auth.login', compact('response'));
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
    public function store(LoginRequest $request)
    {
        $userdata = array(
            'email'     => $request['email'],
            'password'  => $request['password']
        );
        if(Auth::attempt($userdata)) {
            $response = "true";
        }
        else {
            $response = "false";
        }
        return view('auth.login', compact('response'));

        //Session::flash('message-error','Datos son incorrectos');
        //return Redirect::to('/');
    }

    public function logout(){
       session::flush();
        return Redirect::to('/');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
