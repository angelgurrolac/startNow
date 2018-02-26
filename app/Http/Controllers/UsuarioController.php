<?php

namespace startnow\Http\Controllers;

use Illuminate\Http\Request;
use startnow\Http\Requests;
use startnow\Http\Requests\UserCreateRequest;
use startnow\Http\Requests\UserUpdateRequest;
use startnow\user;
use startnow\State;
use Redirect;
use Session;
use startnow\Town;
use DB;



use startnow\Http\Controllers\Controller;
class UsuarioController extends Controller
{

    protected function validator(array $data)
    {
        
       
        return Validator::make($data, [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'Apeido_P' => $request['Apeido_P'],
            'Apeido_M' => $request['Apeido_M'],
            'Direccion' => $request['Direccion'],
            'CP' => $request['CP'],
            'Pais' => $request['Pais'],
            'CD' => $request['CD'],
            'Numero_Ext' => $request['Numero_Ext'],
            'Numero_Cel' => $request['Numero_Cel'],
            'Numero_Casa' => $request['Numero_Casa'],
            'Sex' => $request['Sex'],
            'Fecha' => $request['Fecha'],
            'Perfil' => $request['Perfil'],
          
        ]);
    }


public function __construct(){
        $this->middleware('auth');
        $this->middleware('admin');
        #$this->middleware('@find',['only' => ['edit','update','destroy']]);
    }
    public function find(Route $route){
        $this->user = User::find($route->getParameter('usuario'));



    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    $users = DB::table('users')
        ->join('states', 'states.id', '=', 'users.Pais')
        ->join('towns', 'towns.id', '=', 'users.CD')
        ->select('users.name as usuario','states.name as estado', 'towns.name as municipio', 'users.email','users.id')
        ->distinct()
        ->get();
    #$users= startnow\user::paginate(10);

    
    
    return view ('usuario.index',compact('users'));




    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::pluck('name','id');




        return view('usuario.create',compact('states'));
    }
    
    public function getTowns($id){
            $towns = Town::towns($id);
            return response()->json($towns);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(UserCreateRequest $request)
    {
        \startnow\User::create([

            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'Apeido_P' => $request['Apeido_P'],
            'Apeido_M' => $request['Apeido_M'],
            'Direccion' => $request['Direccion'],
            'CP' => $request['CP'],
            'Pais' => $request->input('Pais'),
            'CD' => $request->input('CD'),
            'Numero_Ext' => $request['Numero_Ext'],
            'Numero_Cel' => $request['Numero_Cel'],
            'Numero_Casa' => $request['Numero_Casa'],
            'Sex' => $request['Sex'],
            'Fecha' => $request['Fecha'],
            'Perfil' => $request['Perfil'],
            
        ]);
        return "usuario registrado";
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
        $user = User::find($id);
        $states = State::pluck('name','id');
        return view('usuario.edit',['user'=>$user], compact('states'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
    
        $user = User::find($id);
        $user->fill($request->all());
        $user->save();
        $states = State::pluck('name','id');
        Session::flash('message','Usuario Actualizado Correctamente');
        return Redirect::to('/usuario');


    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        Session::flash('message','Usuario Eliminado Correctamente');
        return Redirect::to('/usuario');
    }
}