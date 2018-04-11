
<html>
<head>
  <title>Todos</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
    

  {!! Html::script('js/jquery.min.js') !!}
  {!!Html::style('css/welcome.css')!!}
  {!!Html::style('css/navStyle.css')!!}
  <script src="../js/main.js"></script>
  {!!Html::style('css/todos.css')!!}
  {!!Html::style('css/misProyectos.css')!!}
  <style>
    .footer {
      width: 100% !important;
      position: unset !important;
    }
    .crearP {
      padding: 20px;
      border-radius: 5px;
      background-color: #96B454;
      color: #FFF;
      border: none;
      font-size: 1.5em;
    }
  </style>
  
</head>
<body>
  <div class="miContainer">
 
    @include('layouts.nav')
  
    <div class="row text-center opciones margenTop">
      <a href="{!!URL::to('proyectos')!!}" class="col-lg-6 ">MIS PROYECTOS</a>
      <a href="{!!URL::to('proyectos/create')!!}" class="col-lg-6 activa">CREAR PROYECTO</a>
    </div>
    <div class="row" style="margin: 40px 0">
      <div class="proyectoscol-lg-12">
        @include('alerts.request')
        {!!Form::open(['route' => 'proyectos.store', 'method'=>'POST','files' => true])!!}
          @include('proyectos.forms.proyecto')
          @include('proyectos.miembros')
          {{-- @include('proyectos.competencias') --}}
          <div class="text-center">
            {!!Form::submit('Crear Proyecto',['class'=>'crearP'])!!}
          </div>
        {!!Form::close()!!}
      </div>
    </div>
    @include('layouts.footer')
    
  </div>

  <script type="text/javascript" src="{{ asset('js/jquery.formatCurrency-1.4.0.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/money.js') }}"></script>
</body>
</html>

	
		  	