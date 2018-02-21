<html>
<head>
	<title> Informacion proyectos</title>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="../js/main.js"></script>
  {!!Html::style('css/navStyle.css')!!}

  <style type="text/css">
    nav ul {
      width: 100%;
    }
  </style>

</head>



<body>
@include('layouts.nav')

<!-- impresion centrada del nombre del proyecto -->
<center><h2>{!! $proyecto->nombre !!}</h2></center>
<br>


<!--- Creacion de la seccion de la imagen del proyecto traida desde la  BD  -->
<div class="container">
  <div class="row">
    <div class="col-md-6">
	     <img aling=rigth src="../proyectosImg/{!!$proyecto->imagenUrl!!}" class="img-responsive "width="70%" alt="Random Name">
    </div>
<!--- Creacion de la seccion de la tabla muestra datos del proyecto -->
<div class="col-md-6">
  <br><br><br>
<table class="table">
  <thead>
    <tr>
  	<br>
      <th scope="col">Meta Maxima</th>
      <th scope="col">Meta Minima</th>
      <th scope="col">Fecha De Inicio</th>
      <th scope="col">Fecha De Termino</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>${!! $proyecto->metaMax!!}</td>
      <td>${!! $proyecto->metaMin!!}</td>
       <td>{!! $proyecto->fechaInicio!!}</td>
      <td>{!! $proyecto->fechaFin!!}</td>
    </tr>
  </tbody>
</table>

<center><button type="button" class="btn btn-primary btn-lg">Donar</button></center>
<hr></hr>
    </div>
  </div>
<hr></hr>
  </div>
  </div>
</div>
<br>
<br>
<!--- Creacion de la seccion Descripcion larga -->
<div id="band" class="container text-center">
<p align=left>{!!$proyecto->descLarga!!}.</p>
</div>
<br>
<br>

@foreach($etapas as $etapa)

<div class="container">
  @foreach($productos as $producto)
  <h2>Descripcion del Producto.</h2>
  <ul class="list-group">
    <li class="list-group-item active">{!!$producto->nombreProducto!!}</li>
    <li class="list-group-item ">Descripcion:  {!!$producto->descAFondo!!}</li>
    <li class="list-group-item ">Tipo:      {!!$producto->TipoProducto!!}</li>
    <li class="list-group-item ">Etapa:      {!!$etapa->nombreEtapa!!}</li>
    <li class="list-group-item ">Descripcion Etapa :      {!!$etapa->descripcionEtapa!!}</li>
  </ul>
  @endforeach
</div>

@endforeach

 
  <br>
  <br>
<!-- Contenedor del video de cada proyecto --> 
<div class="youtube" class="vid-responsive">

  <div align="center">

          <iframe width="720" height="425" id="vid" src={{$URL}} frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
  </div>
</div>  


<br>
<br>
<!-- creacion de  como llegar a los clientes y propuestas de valor en  tarjetas -->
<center><div class="w3-container">
  <div class="w3-card-4" style="width:75%">
    <header class="w3-container w3-light-blue">
      <h3>Como llegar a los clientes.</h3>
    </header>
    <div class="w3-container">
      <hr>
      <img src="https://cdn.dribbble.com/users/35310/screenshots/3386707/oliver_teaser.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:70px">
      <p align="left">{!!$proyecto->descComollegarClientes!!}</p>
      <br>
    </div>
    
  </div>
</div>
<br>
<br>
  <div class="w3-container">
  <div class="w3-card-4" style="width:75%">
    <header class="w3-container w3-light-blue">
      <h3>Propuesta de valor.</h3>
    </header>
    <div class="w3-container">
      <hr>
      <img src="https://cdn.dribbble.com/users/35310/screenshots/3386707/oliver_teaser.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:70px">
      <p align="left">{!!$proyecto->propuestaValor!!}</p>
      <br>
    </div>
  </div>
</div>
<br>
<br>
<div class="w3-container">
  <div class="w3-card-4" style="width:75%">
    <header class="w3-container w3-light-blue">
      <h3>Valor de Mercado</h3>
    </header>
    <div class="w3-container">
      <hr>
      <img src="https://cdn.dribbble.com/users/35310/screenshots/3386707/oliver_teaser.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:70px">
      <p align="left">{!!$proyecto->valorMercado!!}</p>
      <br>
    </div>
  </div>
</div>
<br>
</center> <!-- centramos los paneles de valor de mercado como llegar a los clientes ETC.-->
<br>

<!--- Impresiones de  ID del proyecto  de manera invisible -->
<INPUT type="hidden" name="idProducto" value="{!! $proyecto->idProducto!!}">
<INPUT type="hidden" name="idUsuario" value="{!! $proyecto->idUsuario!!}">
<INPUT type="hidden" name="idMercado" value="{!! $proyecto->idMercado!!}">
<br>
<br>
<!-- Mercado donde opera -->
 <div align=center class="w3-container">
  @foreach($mercados as $mercado)
  <div class="w3-card-4" style="width:75%">
    <header class="w3-container w3-light-blue">
      <h3>Mercado Donde Opera.</h3>
    </header>
    <div class="w3-container">
      <hr>
      <img src="{!!$mercado->urlMercado!!}" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:100px">
      <p align="left"> {!!$mercado->nombreMercado!!}</p>
      <p align="left"> {!!$mercado->descMercado!!}</p>
      <br>
    </div>
  </div>
  @endforeach
</div>

<br>
<br>
<br>
<!-- Seccion donde se realiza  la competencia directa del produto-->
<hr align=center> </hr>
<h2 align=center> Competencia directa </h2>

<div class="container" align="bottom:100pz;">
<div class="container">
<br>
<br>
<br>
<div class="row justify-content-center">
@foreach($competencias as $competencia )
 <div class="col-sm-4" style="width:100%">
  <div class="w3-card-4" style="width:%100">
    <img src="{!! $competencia->urlImagenCompetencia!!}" alt="Norway" style="width:100%">
    <h2>{!!$competencia->nombreCompetencia!!}</h2>
  <p>{!!$competencia->descripcionCompetencia!!}</p>
  </div>
</div>
@endforeach
</div>
</div>
</div>
<br>
<br>
<!-- Seccion donde se realiza el acomodo del equipo que desarrollo el proyecto -->  

<!--div class="container" align="bottom:100px;"-->
<div aling=center class="container">
<center><h1><strong>Equipo desarrollador.</strong></h1></center>
<br>
<br>
<br>
<div class="row">
@foreach($miembrosequipo as $miembro )
  <div class="col-sm-4">
  <div class="w3-card-4" style="width:%100; text-align:center">
    <h4>{!!$miembro->nombres.' '.$miembro->apellidoP.' '.$miembro->apellidoM!!}</h4>
    <a class="w3-button w3-large w3-teal w3-hide-small" href="{!!$miembro->urlPerfil!!}" title="Linkedin"><i class="fa fa-linkedin"></i></a>
    <img src="{!! $miembro->imagenUrl!!}" alt="Norway" style="width:100%">
    <p>{!!$miembro->puesto!!}</p>
    <p>{!!$miembro->descripcion!!}</p>
  </div>
</div>
@endforeach
<br>
<br>
</div>
<br>
<br>


<!-- Acomodo de las alianzas externas al equipo de desarrollo -->
<h2 align=center> Colaboradores </h2>
<br>
<br>
<br>

@foreach ($alianzas as $alianza)
<div align=center class="container">
  
  <p></p>
  <div class="card" style="width:400px">
    <img class="card-img-top" src="{!!$alianza->urlImagenAlianza!!}" alt="Card image" style="width:100%">
    <div class="card-body">
      <h2>{!!$alianza->nombreAlianza!!}</h2>
      <p class="card-text">{!!$alianza->descripcionAlianza!!}</p>
      <p class="card-text">Some example text.</p>
      <a href="{!!$alianza->urlAlianza!!}" class="btn btn-primary">Visitanos</a>
      @endforeach
    </div>
  </div>
</div>
  <br>
</div>
  <!-- Footer -->
<footer class=" w3-padding-32 w3-theme-d1 w3-center" style="width:100%;">
      
  <h4><strong>Siguenos.</strong></h4>
  <div align=center><img src={{ asset('img/logo2.png') }} class="img-responsive "width="15%" height="15%"></div>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Facebook"><i class="fa fa-facebook"></i></a>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Twitter"><i class="fa fa-twitter"></i></a>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Google +"><i class="fa fa-google-plus"></i></a>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Google +"><i class="fa fa-instagram"></i></a>
  <a class="w3-button w3-large w3-teal w3-hide-small" href="javascript:void(0)" title="Linkedin"><i class="fa fa-linkedin"></i></a>
 

    <div style="position:relative;bottom:100px;z-index:1;" class="w3-tooltip w3-right">
    <span class="w3-text w3-padding w3-teal w3-hide-small">Go To Top</span>   
    <a class="w3-button w3-theme" href="#up"><span class="w3-xlarge">
    <i class="fa fa-chevron-circle-up"></i></span></a>
    </div>
</footer>


</body>
</html>


