<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
  
  
    {!!Html::style('css/bootstrap.min.css')!!}
    {!!Html::style('css/metisMenu.min.css')!!}
    {!!Html::style('css/sb-admin-2.css')!!}
    {!!Html::style('css/login.css')!!}
    {!!Html::style('css/navStyle.css')!!}
    {!!Html::style('css/modalLogin.css')!!}
    {!!Html::style('css/font-awesome.min.css')!!}
</head>
<body>    
    <div class="miContainer">
        <div class="row head">
            <a href="{!!URL::to('/')!!}"><img src="{{ asset('img/logo2.png') }}" alt=""></a>
        </div>
        {{ Form::open(['route' => 'logon.store', 'method'=>'POST']) }}
            <div class="row">
                <div class="form-group col-lg-12">
                    {!!Form::text('email',null,['class'=>'myform-control', 'placeholder'=>'CORREO ELECTRONICO', 'autocomplete' => 'off'])!!}
                </div>
            </div>
            <div class="row" >
                <div class=" form-group col-lg-12">  
                    {!!Form::password('password',['class'=>'myform-control', 'placeholder'=>'CONTRASEÑA', 'autocomplete' => 'off'])!!}
                </div>
            </div>
            <div class="row text-center ">
                {!!Form::label('', 'Usuario o contraseña incorrectos', ['class' => 'text-danger labelError'])!!}
            </div>
            <div class="row justify-content-center">
                {!!Form::submit('INICIAR SESION',['class'=>'Mybtn col-sm-6'])!!}
            </div>
            <div class="row text-center regis">
                <a id="registro" href="{!!URL::to('register')!!}">REGISTRATE</a>
            </div>
        {!!Form::close()!!}
        @include('layouts.footer')
    </div>
    
    @if ($response === "true")
        <script>
            console.log('Response true');
            $('.labelError').css('color', '#fff');
            $(document).ready(function (){
                $('#Mymodal').modal('toggle');
            });
        </script>
        
    @endif
    @if ($response === "false")
        <script>
            console.log('Response false');
            $('.labelError').css('color', '#a94442');
        </script>
    @endif 
    @include('layouts.loginModal')
   

</body>
</html>
