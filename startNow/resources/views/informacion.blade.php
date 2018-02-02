<html>
<head>
	<title> Informacion proyectos</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="css/font-awesome.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <script src="js/jquery-3.2.1.js"></script>
  <script src="js/main.js"></script>

<div align="top-left"><img src={{ asset('img/logo2.png') }} class="img-responsive "width="15%" height="15%"></div>

</head>



<body>
@foreach($proyectos as $proyecto ) 

<center><h2>{!! $proyecto->nombre !!}</h2></center>
<br>


<div class="container">
  <div class="row">
    <div class="col-md-6">
	<!--- Creacion de la seccion de la imagen del proyecto traida desde la  BD  -->
	<img aling=rigth src="{!! $proyecto->imagenUrl!!}" class="img-responsive "width="50%" height="50%" alt="Random Name">
    </div>

<!--- Creacion de la seccion de la tabla muestra datos del proyecto -->
    <div class="col-md-6">
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
      <!--th scope="row">1</th-->
      <td>{!! $proyecto->metaMax!!}</td>
      <td>{!! $proyecto->metaMin!!}</td>
       <td>{!! $proyecto->fechaInicio!!}</td>
      <td>{!! $proyecto->fechaFin!!}</td>
    </tr>
   
  </tbody>
</table>
<center><button type="button" class="btn btn-primary btn-lg">  Donar   </button></center>
<hr></hr>

    </div>
  </div>
<hr></hr>
 

  </div>
   
  </div>
</div>
<!--- Creacion de la seccion Descripcion larga -->

  <div id="band" class="container text-center">
<p align=left>{!!$proyecto->descLarga!!}.</p>
</div>

 
  <br>
  <br>
<!-- Contenedor del video de cada proyecto --> 
<div class="youtube" class="vid-responsive">

  <div align="center">

          <iframe width="420" height="315" id="vid" src={{$URL}} frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
  </div>

</div>  
<br>
<!--- Creacion de la seccion mercado -->
<center><div class="w3-container"><center>
  

  <!--div class="w3-card-4" style="width:65%;">
    <header class="w3-container w3-blue">
      <h1>Valor en el mercado</h1>
    </header>

    <div class="w3-container">
      <p align="left">{!!$proyecto->valorMercado!!}</p>
    </div>
  </div-->
<br>
<br>



<!-- creacion de  como llegar a los clientes y propuestas de valor en  tarjetas -->

<div class="w3-container">
  <div class="w3-card-4" style="width:70%">
    <header class="w3-container w3-light-blue">
      <h3>Como llegar a los clientes.</h3>
    </header>
    <div class="w3-container">
      <hr>
      <img src="https://cdn.dribbble.com/users/35310/screenshots/3386707/oliver_teaser.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
      <p align="left">{!!$proyecto->descComollegarClientes!!}</p>
      <br>
    </div>
    
  </div>
</div>
<br>

  <div class="w3-col s4 w3-white w3-center">
    <p></p>
  </div>

  <div class="w3-container">
 
  <div class="w3-card-4" style="width:70%">
    <header class="w3-container w3-light-blue">
      <h3>Propuesta de valor.</h3>
    </header>
    <div class="w3-container">
      
      <hr>
      <img src="https://cdn.dribbble.com/users/35310/screenshots/3386707/oliver_teaser.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
      <p align="left">{!!$proyecto->propuestaValor!!}</p>
      <br>
    </div>
    
  </div>
</div>
<br>
<!-- Creacion de la seccion de equipo desarrollador del proyecto -->






<!-- Creacion de la seccion del footer soccial -->




<!--Footer-->
<footer class="page-footer indigo center-on-small-only pt-0">

    <!--Footer Links-->
    <div class="container">

        <!--First row-->
        <div class="row">

            <!--First column-->
            <div class="col-md-12">

                <div class="footer-socials mb-5 flex-center">

                    <!--Facebook-->
                    <a class="icons-sm fb-ic"><i class="fa fa-facebook fa-lg white-text mr-md-4"> </i></a>
                    <!--Twitter-->
                    <a class="icons-sm tw-ic"><i class="fa fa-twitter fa-lg white-text mr-md-4"> </i></a>
                    <!--Google +-->
                    <a class="icons-sm gplus-ic"><i class="fa fa-google-plus fa-lg white-text mr-md-4"> </i></a>
                    <!--Linkedin-->
                    <a class="icons-sm li-ic"><i class="fa fa-linkedin fa-lg white-text mr-md-4"> </i></a>
                    <!--Instagram-->
                    <a class="icons-sm ins-ic"><i class="fa fa-instagram fa-lg white-text mr-md-4"> </i></a>
                    <!--Pinterest-->
                    <a class="icons-sm pin-ic"><i class="fa fa-pinterest fa-lg white-text"> </i></a>
                </div>
            </div>
            <!--/First column-->
        </div>
        <!--/First row-->
    </div>
    <!--/Footer Links-->

    <!--Copyright-->
    <div class="footer-copyright">
        <div class="container-fluid">
            © 2016 Copyright: <a href="https://www.MDBootstrap.com"> MDBootstrap.com </a>
        </div>
    </div>
    <!--/Copyright-->

</footer>

</a>

</div>
<br>
<INPUT type="hidden" name="idProducto" value="{!! $proyecto->idProducto!!}">
<INPUT type="hidden" name="idUsuario" value="{!! $proyecto->idUsuario!!}">
<INPUT type="hidden" name="idMercado" value="{!! $proyecto->idMercado!!}">





@endforeach


</body>
</html>