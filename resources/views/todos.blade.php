<html>
<head>
  <title>Todos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="js/jquery-3.2.1.js"></script>
  <script src="js/main.js"></script>
  {!!Html::style('css/bootstrap.min.css')!!}
  {!!Html::style('css/font-awesome.min.css')!!}
  {!!Html::style('css/navStyle.css')!!}
  {!!Html::style('css/todos.css')!!}
  
</head>
<body>
@include('layouts.nav')
  <div class="container-fluid">
    <div class="row">
      <h2 class="col-sm-3">Todos Nuestros Proyectos</h2>
    </div>
  </div>
  <div class="miContainer">
    <div class="row justify-content-center" style="margin: 0 !important;width: 100%;">
      @foreach($proyectos as $proyecto )
        <div class="col-sm-3 carta">
          <div class="row">
            <img src="proyectosImg/{!!$proyecto->imagenUrl!!}" alt="Proyecto" width="100%" height="45%">
          </div>
          <div class="cuerpo">
            <div class="row justify-content-center">
              <span>{!!$proyecto->nombre!!}</span>
            </div>
            <div class="row justify-content-center">
              <p>{!!$proyecto->descCorta!!}</p>
            </div>
          </div>
        </div>
        <script>
          $('.carta').click(function(){
            window.location.href = '{{ url('/info/' . $proyecto->idProyecto . ' ') }}'
            console.log('carta  ')
          });
        </script>
      @endforeach
  </div>
  </div>
</body>
</html>