<html>
<head>
  <title>Todos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue.css">      
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="css/font-awesome.css">
  <script src="js/jquery-3.2.1.js"></script>
  <script src="js/main.js"></script>
  {!!Html::style('css/bootstrap.min.css')!!}
  {!!Html::style('css/metisMenu.min.css')!!}
  {!!Html::style('css/sb-admin-2.css')!!}
  {!!Html::style('css/font-awesome.min.css')!!}
  {!!Html::style('css/navStyle.css')!!}
  <style type="text/css">
    .miContainer{
      margin: 0 100px; 
    }
    p {
        line-height: 1.47;
        height: 100px;
        max-height: 100px;
        overflow: hidden; 
        text-align: left;
        padding: 10px;
    }
    h5 {
      font-weight: bold;
      font-size: 1.3em;
    }
    .card {
      height: 525px;
      max-height: 525px;
      overflow: hidden;
    }
    .card img {
      height: 345px;
      max-height: 345px;
    }
  </style>

</head>
<body>
@include('layouts.nav')
<img src="img/Todosbanner.jpg" style='width:100%; height: 50%;'>



<div class="miContainer text-center bg-grey">
  <h2>Todos Nuestros Proyectos.</h2>
  <br>
    
    @foreach($proyectos as $proyecto )
    <div class="card col-sm-4" style="margin:10px; width:60rem;">
      <img class="card-img-top" src="proyectosImg/{!!$proyecto->imagenUrl!!}" alt="Card image cap">
      <div class="card-body">
        <h5 class="card-title">{!!$proyecto->nombre!!}</h5>
        <p class="card-text">{!!$proyecto->descCorta!!}</p>
      </div>
    </div>
    @endforeach

</div>

</body>
</html>