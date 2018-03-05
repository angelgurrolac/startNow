 </html>

<html lang="en">
<head>
  <title></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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


  

</head>
<body>
    <!--  Creacion  del Carousel manera responsiva -->
    @include('layouts.nav');
<div class="container" align="bottom:100px">
<div align="top-left">

</div>
</div>
<br>
<br>

<div class="container" align="bottom:100pz;"> 
 <div id="myCarousel" class="carousel slide" data-ride="carousel" align="bottom">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <div class="item active">
        <img src="img/ny.jpg" alt="New York" class="img-responsive" >
        <div class="carousel-caption">
          <h3>New York</h3>
          <p>The atmosphere in New York is lorem ipsum.</p>
        </div>      
      </div>

      <div class="item">
        <img src="img/chicago.jpg" alt="Chicago" class="img-responsive" >  <!-- Class="img-responsive" metodo de imagenes responsivas -->
        <div class="carousel-caption">
          <h3>Chicago</h3>
          <p>Thank you, Chicago - A night we won't forget.</p>
        </div>      
      </div>
    
      <div class="item">
        <img src="img/la.jpg" alt="Los Angeles" class="img-responsive" >
        <div class="carousel-caption">
          <h3>LA</h3>
          <p>Even though the traffic was a mess, we had the best time playing at Venice Beach!</p>
        </div>      
      </div>
    </div>


    <!-- Creacion de controles del carousel siguiente o atras  -->
    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>

    </a>
    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
</div>

</div>


<br>
<div class="container 1">
  <center><h3>START NOW !</h3>
  <p><em>We love music!</em></p>
  <p>We have created a fictional band website. Lorem ipsum..</p>
</center>
</div>
<br>


 <!-- Div de proyectos con el arreglo construido para que lo hagarre por default-->
<div class="container" align="bottom:100pz;">
  <div id="proyectos">
    <div class="row">
@foreach($proyectos as $proyecto ) <!-- Creacion del arreglo para que se itere el Div y ahorrar codigo-->
    <a href="{{ url('/info/' . $proyecto->idProyecto . ' ') }}" class='my-link'>
  <div class="col-md-4">   
   <center> <p class="text-center"><center><strong style="height:50px;">{!! $proyecto->nombre !!}</strong></center></p><br> <!-- Impresion de la variable desde la BD-->
    <a href="http://localhost:8080/startnow/public/info" data-toggle="collapse">
      <img src="proyectosImg/{!! $proyecto->imagenUrl!!}" class="img-circle person" width="70%" height="25%" class="img-responsive" alt="Random Name">
    </a>
      <center> <p style="height:150px; width: 250px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{!! $proyecto->descCorta !!}</p> <!-- Impresion de la variable desde la BD-->
  </div>
    </a>
@endforeach
  <div class="center">
      <a href="todos">Ver mas</a>
  </div>
  </div>
  <br>
  </div>
</div>




    <div class="container" align="bottom:100pz;">
    <center><h3>THE BAND</h3> </center>
    <p align=left>We have created a fictional band website. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <br>
    <br>


 <div id="band" class="container text-center">
    <h3 align=rigth>Preguntas Frecuentes </h3>

    <br>
    <br>
  
  <div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
        ¿Como Gano dinero? </a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse in">
      <div class="panel-heading">
        Cuando te vuelves inversionista a través de Play Business, obtienes un pedacito de la Startup (equity). Si la empresa crece, tu inversión aumentará su valor proporcionalmente. Para ganar dinero debes vender tu participación (a esto se le llama exit) y hay varias formas de hacerlo:
        Si la empresa crece, tu inversión aumentará su valor proporcionalmente. Para hacer liquida tu inversion hay varias opciones:  

        * IPO (Oferta pública de inversión): La empresa sale a la bolsa y vendes tu participación 
        * M&A: Compra tu participación un fondo de inversión u otra empresa.
        * Nombre: El emprendedor (maker) u otro inversionista de la comunidad compra tu participación:
        A esto le llamamos exit y es básicamente encontrar a alguien que quiera comprarte el pedazo de la empresa que tienes..</div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
        ¿Que es Crownfunding?</a>
      </h4>
    </div>
    <div id="collapse2" class="panel-collapse collapse">
      <div class="panel-heading">Significa “fondeo de las masas” y son muchas personas haciendo lo imposible, si cada mexicano te da $1 peso, tienes más de $120 millones de pesos en el banco. Crowdfunding es exactamente ese concepto, sólo que se hace a través de una página de internet, de forma organizada y transparente. El que pide dinero ofrece “algo” a cambio y el que entrega dinero recibe esa cosa a cambio. En Play Business te vuelves dueño de una parte de la empresa a cambio de tu dinero..</div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
        ¿Que es equity? </a>
      </h4>
  </div>

    <div id="collapse3" class="panel-collapse collapse">
      <div class="panel-heading">Viene del inglés y se utiliza mucho en este mundo, pero básicamente significa ser acreedor a una parte del dinero que genera o generará una empresa. Específicamente, si una empresa vale $1,000,000 y tú inviertes $10,000 vas a adquirir 1% de equity de la compañía, lo que significa que eres y serás dueño del 1% de todo el dinero que genere la compañía.
      </div>
    </div>

<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">
        ¿Que  gano cuando invierto?</a>
      </h4>
    </div>
    <div id="collapse4" class="panel-collapse collapse">
      <div class="panel-heading">Cuando inviertes en Play Business, por cada startup en la que inviertas, recibirás un certificado de inversión, estos son documentos encriptados y almacenados en nuestra plataforma que avalan tu inversión y por ende te acreditan como dueño. Estos los puedes imprimir o almacenar tu mismo, independientemente de lo que quieras hacer, estarán disponibles por siempre en Play Business.</div>
    </div>

    <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">
        ¿Estamos regulados?</a>
      </h4>
    </div>

    <div id="collapse5" class="panel-collapse collapse">
      <div class="panel-heading">¡Ya casí! La innovación de Play Business nació de un modelo legal diseñado para Startups, aunque tenemos muy buena relación con los reguladores (SHCP y CNBV), aún no tienen las facultades para regularnos. En cuanto salga la Ley Fintech todo cambiará, mientras tanto todos nuestros procesos y experiencia está ayudando a sentar las bases de la Ley.</div>
    </div>
  </div> 
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse6">
        ¿Como operamos legalmente.?</a>
      </h4>
    </div>

    <div id="collapse6" class="panel-collapse collapse collapse">
      <div class="panel-heading">Operamos a través de contratos y firmas electrónicas avanzadas, todos nuestros modelos han sido revisado por firmas de abogados privadas, el equipo interno de Play Business e incluso el regulador CNBV. El modelo está creado para aumentar la eficiencia al máximo, manteniendo la seguridad jurídica como prioridad para todos..</div>
    </div>
  </div>
</div>
</div>
</div>
</div>


<!-- Portafolio de proyectos -->
<div class="container" align="bottom:100pz;">

  <div class="youtube" class="vid-responsive">

  <div align="center">
          <iframe width="900" height="500" id="vid" src="https://www.youtube.com/embed/YDIew2iEvBw" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
  </div>

</div>
</div>
<br>
<br>


<!-- Footer -->
<footer class="w3-container w3-padding-32 w3-theme-d1 w3-center">
  <h4><strong>Siguenos.</strong></h4>
 <div align=center><img src={{ asset('img/logo2.png') }} class="img-responsive "width="15%" height="15%"></div>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Facebook"><i class="fa fa-facebook"></i></a>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Twitter"><i class="fa fa-twitter"></i></a>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Google +"><i class="fa fa-google-plus"></i></a>
  <a class="w3-button w3-large w3-teal" href="javascript:void(0)" title="Google +"><i class="fa fa-instagram"></i></a>
  <a class="w3-button w3-large w3-teal w3-hide-small" href="javascript:void(0)" title="Linkedin"><i class="fa fa-linkedin"></i></a>
 

  <div style="position:relative;bottom:100px;z-index:1;" class="w3-tooltip w3-right">   
    <a class="w3-button w3-theme" href="#up"><span class="w3-xlarge">
    <i class="fa fa-chevron-circle-up"></i></span></a>
  </div>
  </footer>

</body>
</html>


