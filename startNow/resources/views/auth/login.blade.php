@extends('layouts.app')

@section('content')

<div class="header-info">
                <h1>BIG HERO 6</h1>
              {{ Form::open(array('url' => 'logon')) }}
                    <div class="form-group">
                        {!!Form::label('correo','Correo:')!!}   
                        {!!Form::email('email',null,['class'=>'form-control', 'placeholder'=>'Ingresa tu correo'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('contrasena','Contraseña:')!!}   
                        {!!Form::password('password',['class'=>'form-control', 'placeholder'=>'Ingresa tu contraseña'])!!}
                    </div>
                    {!!Form::submit('Iniciar',['class'=>'btn btn-primary'])!!}
                {!!Form::close()!!}
            </div>
        </div>






@endsection
