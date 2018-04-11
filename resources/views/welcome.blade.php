 
<html lang="en">
<head>
  <title></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
  
    {!!Html::style('css/metisMenu.min.css')!!}
    {!!Html::style('css/welcome.css')!!}
    {!!Html::style('css/navStyle.css')!!}
    <script src="js/main.js"></script>
</head>
<body>    
  <div class="miContainer">
    <header>
        @include('layouts.nav')
    </header>
    <section id="banner">
      <div id="headText" unselectable="on" onselectstart="return false;" onmousedown="return false;">
        <span class="textoSpan" id="inova">Inovación</span> <br>
        <span class="textoSpan" id="tecno">Tecnológica</span> <br>
        <span class="textoSpan" id="apoyo">Apoya a los mejores Startups de inovación tecnologica</span>
      </div>
    </section>
    <div class="row startUps">
      <div class="col-lg-4 text-center">
        <span class="topSpan">STARTUPS FINALIZADOS</span>
        <span class="numeroSpan">{!!$finalizados!!}</span>
      </div>
      <div class="col-lg-4 text-center">
        <span class="topSpan">STARTUPS APOYADOS</span>
        <span class="numeroSpan">{!!$apoyados!!}</span>
      </div>
      <div class="col-lg-4 text-center">
        <span class="topSpan">PERSONAS APOYANDO</span>
        <span class="numeroSpan">{!!$apoyando!!}</span>
      </div>
    </div>

    <div class="row categorias">
      {{-- <div class="col-lg-1"></div> --}}
      <div class="texto col-lg-9">
        <span>PROYECTOS DE    </span><span class="colorTexto">INOVACION</span>
        <div class="linea"></div>
      </div>
      <div class="col-lg-3 boton">
        <a href="{!!URL::to('todos')!!}" class="btn-lg btn-primary">CATEGORIAS</a>
      </div>
    </div>

   <!-- Div de proyectos con el arreglo construido para que lo hagarre por default-->
      <div class="row proyectos">
        @foreach($randoms as $random ) <!-- Creacion del arreglo para que se itere el Div y ahorrar codigo-->
            <div class="col-md-4 text-center" style="text-align: center;">
              <a style="text-decoration: none;" href="{{ url('/info/' . $random->idProyecto . ' ') }}" class='my-link'>
                <p>{!! $random->nombre !!}</p>
                <img src="proyectosImg/{!! $random->imagenUrl!!}" class="img-circle img-responsive" style="height: 320px;width: 100%;" alt="Random Name">
              </a>
            </div>
        @endforeach
      </div>

      <div class="row text-center quienes">
        <h3>¿QUIENES SOMOS?</h3>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempora iusto fugit, quis veniam autem illo optio dolores facilis, facere, molestiae voluptatem reprehenderit reiciendis, quas molestias alias nihil? Dolores, nihil similique!
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus sint possimus cupiditate quibusdam impedit, harum error dolorem doloribus voluptatum quidem natus, voluptas adipisci doloremque velit consequatur magni libero rem nobis!
        </p>
      </div>
      
    <div class="row preguntas">
        <div class="texto col-lg-11">
          <span>PREGUNTAS    </span><span class="colorTexto">FRECUENTES</span>
          <div class="linea"></div>
        </div>
        <div class="col-lg-1 interrogacion colorTexto">
            <span>?</span>
        </div>
    </div>

    <div class="row">    
      <div class="panel-group" id="accordion">

        <div class="panel panel-default">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse">
            <div class="panel-heading">
              <h4 class="panel-title panelTitulo">¿Como Gano dinero?</h4>
            </div>
          </a>
          <div id="collapse" class="panel-collapse collapse text-justify">
            <div class="panel-heading panelTexto">
              <p>
                Cuando te vuelves inversionista a través de Play Business, obtienes un pedacito de la Startup (equity). Si la empresa crece, tu inversión aumentará su valor proporcionalmente. <br>
                Para ganar dinero debes vender tu participación (a esto se le llama exit) y hay varias formas de hacerlo:
              </p> 
              <p>
                Si la empresa crece, tu inversión aumentará su valor proporcionalmente. Para hacer liquida tu inversion hay varias opciones:
              </p>
              <ul>
                <li>IPO (Oferta pública de inversión): La empresa sale a la bolsa y vendes tu participación</li>
                <li>M&A: Compra tu participación un fondo de inversión u otra empresa.</li>
                <li>Nombre: El emprendedor (maker) u otro inversionista de la comunidad compra tu participación:</li>
              </ul>
              <p>
                A esto le llamamos exit y es básicamente encontrar a alguien que quiera comprarte el pedazo de la empresa que tienes..
              </p>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
            <div class="panel-heading">
              <h4 class="panel-title panelTitulo">¿Que es crowfunding?</h4>
            </div>
          </a>
          <div id="collapse1" class="panel-collapse collapse text-justify">
            <div class="panel-heading panelTexto">
              <p>
                El crowdfunding, también conocido como micromecenazgo o financiación colectiva, es una alternativa excelente para aquellos que tienen un proyecto, sea en el ámbito que sea, y que buscan financiación para poder llevarlo a cabo.
              </p> 
              <p>
                Este sistema de mecenazgo se da mediante plataformas destinadas. El objetivo consiste en recaudar fondos para que algunas personas (miles de personas en realidad) entren como inversores y financien mediante pequeñas cantidades de dinero los proyectos publicados.
              </p>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
            <div class="panel-heading">
              <h4 class="panel-title panelTitulo">¿Que es equity?</h4>
            </div>
          </a>
          <div id="collapse2" class="panel-collapse collapse text-justify">
            <div class="panel-heading panelTexto">
              <p>
                El equity crowdfunding es una forma de financiación colectiva en la que varios inversores hacen su aportación de capital en una empresa a cambio de un porcentaje de la misma.
              </p> 
              <p>
                Este tipo de inversión, en la mayoría de los casos, suele darse en startups o empresas de reciente creación, que se encuentran en fases iniciales y requieren de apoyo económico para hacer despegar su proyecto.
              </p>
              <p class="text-danger">*El porcentaje de equity cambia segun el crecimiento o valor de tu empresa</p>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
            <div class="panel-heading">
              <h4 class="panel-title panelTitulo">¿Que gano cuando invierto?</h4>
            </div>
          </a>
          <div id="collapse3" class="panel-collapse collapse text-justify">
            <div class="panel-heading panelTexto">
              <p>
                Cuando inviertes en StartNow, por cada startup en la que inviertas, recibiras un certificado de inversión, estos documentos son encriptados y almacenados en nuestra plataforma que avalan tu inversión y por ende te acreditan como dueño. Estos los puedes imprimir o almacenar tu mismo, independientemente de lo que quieras hacer, estaran disponibles por siempre en StartNow
              </p>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">
            <div class="panel-heading">
              <h4 class="panel-title panelTitulo">¿estamos regulados?</h4>
            </div>
          </a>
          <div id="collapse4" class="panel-collapse collapse text-justify">
            <div class="panel-heading panelTexto">
              <p>
                ¡Ya casí! La inovacion de StartNow nacío de un modelo legal diseñado para Startups, aunque tenemos muy buena relación con los reguladores (SHCP y CNBV), aun no tienen las facultades para regularnos. En cuanto salga la Ley Fintech todo cambiara, mientras tanto todos nnuestros procesos y experiencia estan ayudando a sentar las bases de esta Ley.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row newProy text-center">
      <h1>NUEVOS PROYECTOS</h1>
    </div>
    <div class="row newProyImg">
      @foreach($proyectos as $proyecto ) <!-- Creacion del arreglo para que se itere el Div y ahorrar codigo-->
          <a href="{{ url('/info/' . $proyecto->idProyecto . ' ') }}">
            <div class="col-md-4">
              <img src="proyectosImg/{!! $proyecto->imagenUrl!!}" class="img person" width="100%" height="30%" alt="Random Name">
            </div>
          </a>
        @endforeach
    </div>
    @include('layouts.footer')
  </div>

</body>
</html>


