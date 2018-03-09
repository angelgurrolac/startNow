@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            {{ Form::open(array('url' => 'logon')) }}
                <div class="form-group">
                        {!!Form::label('email','Correo:')!!}   
                        {!!Form::email('email',null,['class'=>'form-control', 'placeholder'=>'Ingresa tu correo'])!!}
          
                </div>
                <div class=" form-group">
                    {!!Form::label('password','Contraseña:')!!}   
                    {!!Form::password('password',['class'=>'form-control', 'placeholder'=>'Ingresa tu contraseña'])!!}
                </div>
                    <div class="col-sm-5"></div>
                    {!!Form::submit('Iniciar',['class'=>'btn btn-primary col-sm-2'])!!}
            {!!Form::close()!!}
        </div>
    </div>
    
@endsection
