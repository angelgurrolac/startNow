<div class="menu_bar">
      <a href="#" class="bt-menu"><span class="fa fa-align-justify"></span>Menu</a>
</div>
  <nav class="nav" id="up">
      <a href="{!!URL::to('/')!!}"><img src="{{ asset('img/logo2.png') }}"></a>
      <ul>
          @if(isset(Auth::user()->id))
            <li><a class="whiteLink" href="{!!URL::to('logout')!!}">CERRAR SESION</a></li>
            <li><a class="whiteLink active" href="{!!URL::to('proyectos')!!}">MIS PROYECTOS</a></li>
            <li><a class="whiteLink" href="{!!URL::to('todos')!!}">TODOS LOS PROYECTOS</a></li>
            <li><span class="greenLink">{!!Auth::user()->name!!}</span></li>
          @else
            <li><a id="registrate" href="{!!URL::to('register')!!}">REGÍSTRATE</a></li>
            <li><a id="login" href="{!!URL::to('logon')!!}">INICIAR SESIÓN</a></li>
          @endif
        {{-- <li class="submenu">
            <a href="#"><span class="fa fa-user"></span> {!!Auth::user()->name!!}</a>
            <ul class="children">
              <li><a href="{!!URL::to('/logout')!!}"><span class="fa fa-sign-out"></span>LogOut</a></li>
            </ul>
        </li> --}}
      </ul>
  </nav>