<?php

namespace startnow\Http\Controllers\Auth;

use startnow\User;
use Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use startnow\Http\Controllers\Controller;
use startnow\State;
use startnow\Town;
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
    {
        // $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // dd($data);
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'Apeido_P' => 'required|string|max:32',
            'Apeido_M' => 'required|string|max:32',
            'Direccion' => 'required|string|max:255',
            'CP' => 'required|numeric|digits:5',
            'Pais' => 'required|string|max:32',
            'CD' => 'required|string|max:32',
            'Numero_Cel' => 'required|numeric|digits:10',
            'Numero_Casa' => 'required|numeric|digits_between:7,10',
            'Sex' => 'required|string|max:10',
            'Fecha' => 'required|date',
            'Fecha' => 'required|date',
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
            'remember_token' => $data['_token'],
            'email' => $data['email'],
            'Apeido_P' => $data['Apeido_P'],
            'Apeido_M' => $data['Apeido_M'],
            'Direccion' => $data['Direccion'],
            'CP' => $data['CP'],
            'Pais' => $data['Pais'],
            'CD' => $data['CD'],
            'Numero_Cel' => $data['Numero_Cel'],
            'Numero_Casa' => $data['Numero_Casa'],
            'Sex' => $data['Sex'],
            'Fecha' => $data['Fecha'],
            'password' => $data['password'],
        ]);
    }
}
