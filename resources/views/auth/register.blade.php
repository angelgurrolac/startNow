@extends('layouts.app')

@section('content')
<div class="container">
    <br>
    <br>
    <div class="row">
        <div class="col-md-10 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">Registro de usuarios.</div>
                <div class="panel-body">
                    <form class="form-inline" method="POST" action="{{ route('register') }}" aling="center">
                        {{ csrf_field() }}
                        <BR>
                        <BR>
                        <br>
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }} col-md-6 ">
                            <label for="name" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <br>
                        <div class="form-group  {{ $errors->has('Apeido_P') ? ' has-error' : '' }} col-md-6  ">
                            <label for="Apeido_P" class="col-md-4 control-label">Apellido Paterno</label>

                            <div class="col-md-6">
                                <input id="Apeido_P" type="text" class="form-control" name="Apeido_P" value="{{ old('Apeido_P') }}" required autofocus>

                                @if ($errors->has('Apeido_P'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Apeido_P') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>
                          <BR>
                          <div class="form-group {{ $errors->has('Apeido_M') ? ' has-error' : '' }} col-md-6 ">
                            <label for="Apeido_M" class="col-md-4 control-label">Apellido Materno</label>

                            <div class="col-md-6">
                                <input id="Apeido_M" type="text" class="form-control" name="Apeido_M" value="{{ old('Apeido_M') }}" required autofocus>

                                @if ($errors->has('Apeido_M'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Apeido_M') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>


                         <div class="form-group  {{ $errors->has('Direccion') ? ' has-error' : '' }} col-md-6 ">
                            <label for="Direccion" class="col-md-4 control-label">Direccion</label>

                            <div class="col-md-6">
                                <input id="Direccion" type="text" class="form-control" name="Direccion" value="{{ old('Direccion') }}" required autofocus>

                                @if ($errors->has('Direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>



                         <div class="form-group  {{ $errors->has('CP') ? ' has-error' : '' }} col-md-6  ">
                            <label for="CP" class="col-md-4 control-label">Codigo Postal</label>

                            <div class="col-md-6">
                                <input id="CP" type="text" class="form-control" name="CP" value="{{ old('CP') }}" required autofocus>

                                @if ($errors->has('CP'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('CP') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>


                         <div class="form-group {{ $errors->has('Numero_Ext') ? ' has-error' : '' }} col-md-6  ">
                            <label for="Numero_Ext" class="col-md-4 control-label">Numero Exterior</label>

                            <div class="col-md-6">
                                <input id="Numero_Ext" type="text" class="form-control" name="Numero_Ext" value="{{ old('Numero_Ext') }}" required autofocus>

                                @if ($errors->has('Numero_Ext'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Numero_Ext') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>



                        <div class="form-group col-md-6 ">
                            <label for="Pais" class="col-md-4 control-label">Pais</label>
                            <div class="col-md-6">
                                <select class="form-control" name="Pais">
                                    <option>Mexico</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                </select>

                            </div>
                      </div> 
                        <br>
 

                      <div class="form-group col-md-6 ">
                            <label for="CD" class="col-md-4 control-label">Ciudad</label>
                            <div class="col-md-6">
                                <select class="form-control" name="CD">
                                    <option>Durango</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                </select>

                            </div>
                      </div> 
                        <br>
 
                     
                      <div class="form-group  {{ $errors->has('Numero_Cel') ? ' has-error' : '' }} col-md-6 ">

                            <label for="Numero_Cel" class="col-md-4 control-label">Numero Celular</label>

                            <div class="col-md-6">
                                <input id="Numero_Cel" type="text" class="form-control" name="Numero_Cel" value="{{ old('Numero_Cel') }}" required autofocus>

                                @if ($errors->has('Numero_Cel'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Numero_Cel') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                         <br>

                         <br>
                        <div class="form-group  {{ $errors->has('Numero_Casa') ? ' has-error' : '' }} col-md-6 ">
                            <label for="Numero_Casa" class="col-md-4 control-label">Numero Casa</label>

                            <div class="col-md-6">
                                <input id="Numero_Casa" type="text" class="form-control" name="Numero_Casa" value="{{ old('Numero_Casa') }}" required autofocus>

                                @if ($errors->has('Numero_Casa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Numero_Casa') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                        </div>
                         <div class="form-group col-md-6 ">
                            <label for="Sex" class="col-md-4 control-label">Sexo</label>
                            <div class="col-md-6">
                                <select class="form-control" name="Sex">
                                    <option>Seleccione uno</option>
                                    <option>Masculino</option>
                                    <option>Femenino</option>
                                    
                                </select>

                            </div>
                      </div>
                        <br>
 
                      <br>
                      <div class="form-group  {{ $errors->has('Fecha') ? ' has-error' : '' }} col-md-6 ">
                            <label for="Fecha" class="col-md-4 control-label">Fecha De Nacimiento</label>

                            <div class="col-md-6">
                                <input id="Fecha" type="text" class="form-control" name="Fecha" value="{{ old('Fecha') }}" required autofocus>

                                @if ($errors->has('Fecha'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Fecha') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>

                            <br>

                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} col-md-6 ">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <br>

                            <br>

                        <div class="form-group  {{ $errors->has('password') ? ' has-error' : '' }} col-md-6  ">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group col-md-6 ">
                            <label for="Perfil" class="col-md-4 control-label">Perfil</label>
                            <div class="col-md-6">
                                <select class="form-control" name="Perfil">
                                    <option>Seleccione uno</option>
                                    <option>Inversionista</option>
                                    <option>Emprendedor</option>
                                </select>

                            </div>
                      </div> 
                        <br>

    
                        <div class="form-group col-md-6  " >
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                          


                        <div class="form-group col-md-6  alineacion" >
                            <div class="col-md-7 col-md-offset-6">
                                <br>
                                <button type="submit" class="btn btn-primary btn-lg"  >
                                    Crear usuario
                                </button>
                            </div>
                        </div>
                          <br>

                    </form>

                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
