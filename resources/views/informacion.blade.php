<html>
<head>
	<title> Informacion proyectos</title>

   <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
   <script type="text/javascript" src="https://conektaapi.s3.amazonaws.com/v0.3.0/js/conekta.js"></script>
        <script type="text/javascript">
            // Conekta Public Key
            Conekta.setPublishableKey('key_GPUKe83pUTXqr7UdKsXyWBw');
            Conekta.setPublicKey('key_GPUKe83pUTXqr7UdKsXyWBw');
            Conekta.setLanguage("es");
            Conekta.getLanguage();
            // ...
        </script>
  <script src="../js/main.js"></script>
  {!!Html::style('css/navStyle.css')!!}
  {!!Html::style('css/todos.css')!!}
  {!!Html::style('css/info.css')!!}

</head>

<body>
  <div class="miContainer">
    @include('layouts.nav')
  </div><br><br><br>

<!--- Creacion de la seccion de la imagen del proyecto traida desde la  BD  -->
<div class="miContainer">
  <div class="row banner">
    <div id="headText" class="text-center" unselectable="on" onselectstart="return false;" onmousedown="return false;">
      <span class="textoSpan">{!! $proyecto->nombre !!}</span>
    </div>
  </div>

  <div class="row margen-top apoyo text-center">
    <div class="row">
      <button type="button" data-toggle="modal" data-target="#modal" class="">APOYAR PROYECTO</button>
    </div>
    <div class="row">
      <div class="row">
        <img src="../proyectosImg/{!!$proyecto->imagenUrl!!}" alt="ImgProyecto">
      </div>
      <div class="row metas">
        <span>META MINIMA: <strong>{!!$proyecto->metaMin!!}</strong></span>
        <span>META MAXIMA: <strong>{!!$proyecto->metaMax!!}</strong></span>
        <div class="row fechas">
          <span class="col-lg-6">INICIO: <strong>{!!$proyecto->fechaInicio!!}</strong></span>
          <span class="col-lg-6">FIN: <strong>{!!$proyecto->fechaFin!!}</strong></span>
        </div>

      </div>
    </div>
</div>
    <div class="row margen-top text-left">
        <h3>Introducción</h3>
        <div class="col-lg-12 linea"></div>
        <p>{!! $proyecto->descCorta !!}</p>
        <div class="row margen-top text-center">
          <iframe width="720" height="425" id="vid" src={{$URL}} frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe> 
        </div>
    </div>
    <div class="row margen-top text-left">
        <h3>Descripción</h3>
        <div class="col-lg-12 linea"></div>
        <p>{!! $proyecto->descLarga !!}</p>
    </div>
    <div class="row margen-top text-left">
        <h3>Mercado</h3>
        <div class="col-lg-12 linea"></div>
        <p>{!! $proyecto->valorMercado !!}</p>
    </div>

    <div class="row margen-top text-left">
        <div class="col-lg-6">
          <h3 class="margenVerde">Como llegar al cliente</h3>
          <p>{!! $proyecto->descComollegarClientes !!}</p>
        </div>
        <div class="col-lg-6">
          <h3 class="margenVerde">Propuesta de valor</h3>
          <p>{!! $proyecto->propuestaValor !!}</p>
        </div>
    </div>

    <div class="row margen-top text-center">
      <div class="row">
        <h2>EQUIPO</h2>
      </div>
      <div class="row">
        @foreach($miembrosequipo as $miembro ) 
          @if(count($miembrosequipo) > 3)
            <div class="col-sm-3 text-center"> 
              <div class="w3-card-3" style="text-align:center"> 
                <img src="../proyectosImg/{{$miembro->imagenMiembro}}" alt="Norway" style="width:100%">
                <a class="linked" href="{!!$miembro->urlPerfil!!}" title="Linkedin"><i class="fab fa-linkedin"></i></a> 
                <h4 class="nombre">{!!$miembro->nombres.' '.$miembro->apellidoP.' '.$miembro->apellidoM!!}</h4> 
                <p>{!!$miembro->puesto!!}</p> 
              </div> 
            </div>
          @else
            <div class="col-sm-4 text-center"> 
              <div class="w3-card-3" style="text-align:center"> 
                <img src="../proyectosImg/{{$miembro->imagenMiembro}}" alt="Norway" style="width:100%">
                <a class="linked" href="{!!$miembro->urlPerfil!!}" title="Linkedin"><i class="fab fa-linkedin"></i></a> 
                <h4 class="nombre">{!!$miembro->nombres.' '.$miembro->apellidoP.' '.$miembro->apellidoM!!}</h4> 
                <p>{!!$miembro->puesto!!}</p> 
              </div> 
            </div>
          @endif
        @endforeach
    </div>
  </div>
  @include('layouts.footer')
</div>
<script type="text/javascript"> 
            jQuery(function($) { 
                 
                 
                var conektaSuccessResponseHandler; 
                conektaSuccessResponseHandler = function(token) { 
                    var $form; 
                    $form = $("#card-form"); 
 
                    /* Inserta el token_id en la forma para que se envíe al servidor */ 
                    $form.append($("<input type=\"hidden\" name=\"conektaTokenId\" />").val(token.id)); 
 
                    /* and submit */ 
                    $form.get(0).submit(); 
                }; 
                 
                conektaErrorResponseHandler = function(token) { 
                    console.log(token);
                    var $form = $("#card-form");
                    $form.find(".card-errors").text(token.message_to_purchaser);
                    $form.find("button").prop("disabled", false);
                }; 
                 
                $("#card-form").submit(function(event) { 
                    event.preventDefault(); 
                    var $form; 
                    $form = $(this); 
 
                    /* Previene hacer submit más de una vez */ 
                    $form.find("button").prop("disabled", true); 
                    Conekta.token.create($form, conektaSuccessResponseHandler, conektaErrorResponseHandler); 
                    /* Previene que la información de la forma sea enviada al servidor */ 
                    return false; 
                }); 
 
            }); 
 
        </script> 
  @include('layouts.modalPago')
</body>
</html>


