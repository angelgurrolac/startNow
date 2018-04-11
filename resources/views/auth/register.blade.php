<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  
    {!!Html::style('css/registro.css')!!}
    {!!Html::style('css/datepicker.css')!!}
    
</head>
<body>    
    <div class="miContainer">
        <div class="row head">
            <a href="{!!URL::to('/')!!}"><img src="{{ asset('img/logo2.png') }}" alt=""></a>
        </div>
        <div class="row">
            <form class="form" method="POST" action="{{ route('register') }}" aling="center">
                {{ csrf_field() }}
                <div class="row form-group">
                    <input type="text" id="name" name="name" placeholder="Nombre" class="col-md-5">
                    <div class="col-md-2"></div>
                    <input type="text" id="Apeido_P" name="Apeido_P" placeholder="Apellido Paterno" class="col-md-5">
                </div>
                <div class="row form-group">
                    <input type="text" id="Apeido_M" name="Apeido_M" placeholder="Apellido Materno" class="col-md-5">
                    <div class="col-md-2"></div>
                    <input type="email" id="email" name="email" placeholder="Correo electronico" class="col-md-5">
                </div>
                <div class="row form-group">
                    <input type="text" id="Direccion" name="Direccion" placeholder="Dirección" class="col-md-5">
                    <div class="col-md-2"></div>
                    <input type="text" id="CP" name="CP" placeholder="Codigo Postal" class="col-md-5">
                </div>
                <div class="row form-group">
                    <input type="text" id="Pais" name="Pais" placeholder="País" class="col-md-5">
                    <div class="col-md-2"></div>
                    <input type="text" id="CD" name="CD" placeholder="Ciudad" class="col-md-5">
                </div>
                <div class="row form-group">
                    <input type="text" id="Numero_Cel" name="Numero_Cel" placeholder="Celular" class="col-md-5">
                    <div class="col-md-2"></div>
                    <input type="number" id="Numero_Casa" name="Numero_Casa" placeholder="Telefono" class="col-md-5">
                </div>
                <div class="row form-group">
                    <select name="Sex" id="sexo" class="col-md-5">
                        <option value="" selected disabled>Sexo</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                    <div class="col-md-2"></div>
                    <input type="text" id="Fecha" name="Fecha" class=" col-md-5" data-toggle="datepicker" placeholder="Fecha de Nacimiento">
                </div>
                <div class="row form-group">
                    <input type="password" id="password" name="password" placeholder="Contraseña" class="col-md-5">
                    <div class="col-md-2"></div>
                    <input type="password" name="password_confirmation" placeholder="Confirma tu contraseña" class="col-md-5">
                </div>
                <div class="row">
                        <div class="col-md-3"></div>
                        <input type="submit" class="col-md-6 button" value="Crear Cuenta">
                        <div class="col-md-3"></div>
                </div>
            </form>
        </div>
        @include('layouts.footer')
    </div>
    @if ($errors->any())
        @php
             // dd($errors);
        @endphp
    @endif
    @if ($errors->has('name'))
        <script>
            $('#name').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Apeido_P'))
        <script>
            $('#Apeido_P').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Apeido_M'))
        <script>
            $('#Apeido_M').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('email'))
        <script>
            $('#email').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Direccion'))
        <script>
            $('#Direccion').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('CP'))
        <script>
            $('#CP').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Pais'))
        <script>
            $('#Pais').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('CD'))
        <script>
            $('#CD').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Numero_Cel'))
        <script>
            $('#Numero_Cel').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Numero_Casa'))
        <script>
            $('#Numero_Casa').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Sex'))
        <script>
            $('#sexo').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('Fecha'))
        <script>
            $('#Fecha').addClass('alert-danger');
        </script>
    @endif
    @if ($errors->has('password'))
        <script>
            $('#password').addClass('alert-danger');
            $('#password_confirmation').addClass('alert-danger');
        </script>
    @endif

    {!!Html::script('js/datepicker.js') !!}
    @include('layouts.loginModal')
    <script>
        $(function() {
          $('[data-toggle="datepicker"]').datepicker({
            autoHide: true,
            zIndex: 2048,
            format: 'yyyy-mm-dd',
          });
        });
        // function modal() {
        //     $(document).ready(function (){
        //         console.log('El modal');
        //         $('#Mymodal').modal('toggle');
        //     });
        // }
  </script>
  
</body>
</html>
