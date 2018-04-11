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
  
</head>
<body>
  <div class="miContainer">
    <header>
        @include('layouts.nav')
    </header>

    <div class="row banner">
        <div id="headText" class="text-center" unselectable="on" onselectstart="return false;" onmousedown="return false;">
        <span class="textoSpan">Proyectos Tecnologicos</span>
      </div>
    </div>

    <div class="row" style="margin-top: 5%;">
      <div class="categorias col-lg-3">
        <h5>Categorías</h5>
        <ul>
          <li><a href="{!!URL::to('todos')!!}">Todos</a></li>
          @foreach ($categorias as $categoria)
            <li><a href="{!!URL::to('todos/'.$categoria->id)!!}">{{$categoria->name}}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="proyectos col-lg-9">
        @if ($proyectos->isEmpty())
          <div class="text-center filterEmpty">
            <span>No hay proyectos en esta categoria</span>
          </div>
        @else
          @foreach($proyectos as $proyecto )
            <a href="{!!URL::to('info/'.$proyecto->idProyecto)!!}">
              <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="project">
                    <figure class="img-responsive">
                        <img src="{{URL::asset('proyectosImg/'.$proyecto->imagenUrl)}}">
                        <span class="actions">
                            <span class="project-details">{!!$proyecto->nombre!!}</span>
                            <div class="descripcion">
                              <span class="project-details">{!!$proyecto->descCorta!!}</span>
                            </div>
                            <div class="margen">
                              <span class="project-details margen">Meta minima: {!!$proyecto->metaMin!!}</span> 
                            </div>
                        </span>
                    </figure>
                </div>
              </div>
            </a>
          @endforeach
        @endif
        
        
      </div>
    </div>
    <div class="row">
      <center>{!!$proyectos->links()!!}</center>
    </div>
    @include('layouts.footer')
  </div>
</body>
</html>