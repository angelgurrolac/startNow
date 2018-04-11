<html>
<head>
  <title>Todos</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
  
  {!!Html::style('css/welcome.css')!!}
  {!!Html::style('css/navStyle.css')!!}
  <script src="js/main.js"></script>
  {!!Html::style('css/todos.css')!!}
  {!!Html::style('css/misProyectos.css')!!}
  
</head>
<body>
  <div class="miContainer" style="margin-bottom: 10%">
 
    @include('layouts.nav')
    
    <div class="row text-center opciones margenTop" >
      <a href="{!!URL::to('proyectos')!!}" class="col-lg-6 activa">MIS PROYECTOS</a>
      <a href="{!!URL::to('proyectos/create')!!}" class="col-lg-6">CREAR PROYECTO</a>
    </div>
    <div class="row" style="margin-top: 5%">
      <div class="proyectos col-lg-12">
        @foreach($proyectos as $proyecto )
          <a href="info/{!!$proyecto->idProyecto!!}">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
              <div class="project">
                  <figure class="img-responsive">
                      <img src="proyectosImg/{!!$proyecto->imagenUrl!!}">
                      <span class="actions">
                          <span class="project-details">{!!$proyecto->nombre!!}</span>
                          <div class="descripcion">
                            <span class="project-details">{!!$proyecto->descCorta!!}</span>
                          </div>
                      </span>
                  </figure>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
    
  </div>
@include('layouts.footer')
  
</body>
</html>