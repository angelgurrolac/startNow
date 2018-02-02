<?php

namespace startnow\Http\Controllers\Auth;

use startnow\User;
use startnow\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        
       
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \startnow\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'Apeido_P' => $data['Apeido_P'],
            'Apeido_M' => $data['Apeido_M'],
            'Direccion' => $data['Direccion'],
            'CP' => $data['CP'],
            'Pais' => $data['Pais'],
            'CD' => $data['CD'],
            'Numero_Ext' => $data['Numero_Ext'],
            'Numero_Cel' => $data['Numero_Cel'],
            'Numero_Casa' => $data['Numero_Casa'],
            'Sex' => $data['Sex'],
            'Fecha' => $data['Fecha'],
            'Perfil' => $data['Perfil'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
