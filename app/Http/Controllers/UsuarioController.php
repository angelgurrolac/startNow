<?php

namespace startnow\Http\Controllers;

use Illuminate\Http\Request;
use startnow\Http\Requests;
use startnow\Http\Requests\UserCreateRequest;
use startnow\Http\Requests\UserUpdateRequest;
use startnow\user;
use Redirect;
use Session;

use startnow\Http\Controllers\Controller;
class UsuarioController extends Controller
{

    protected function validator(array $data)
    {
        
       
        return Validator::make($data, [
            'name' => 'required|string|max:50',
            'email' => 'required|unique:users|string|max:290',
            'password' => 'required|string|max:15',
            'Apeido_P' => 'required|string|max:50',
            'Apeido_M' => 'required|string|max:50',
            'metaMin' => 'required|money_format|max:7',
            'metaMax' => 'required|numeric|max:7',
            'Direccion' => 'required|string|max:300',
            'CP' => 'required|numeric|max:10',
            'Pais' => 'required|string|max:100',
            'Numero_Ext' => 'required|numeric|max:10',
            'Numero_Cel' => 'required|numeric|max:10',
            'Numero_Casa' => 'required|numeric|max:10',
            'Sex' => 'required|string|max:10',
            'Fecha' => 'required|date(format)|max:10',
            'Perfil' => 'required|string|max:100',
          
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
    
    $users= \startnow\user::paginate(10);
    return view ('usuario.index',compact('users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('usuario.create');
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
            'password' => bcrypt($request['password']),
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
        return "Usuario registrado";
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
        return view('usuario.edit',['user'=>$user]);
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