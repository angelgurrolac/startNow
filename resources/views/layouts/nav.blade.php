<div class="menu_bar">
      <a href="#" class="bt-menu"><span class="fa fa-align-justify"></span>Menu</a>
</div>
  <nav class="nav" id="up">
      <ul>
        <img src="{{ asset('img/logo2.png') }}" class="img-responsive">
        <li><a href="{!!URL::to('home')!!}">Home</a></li>
        <li><a href="{!!URL::to('todos')!!}">Proyectos</a></li>
        <li><a href="{!!URL::to('proyectos')!!}">Mis proyectos</a></li>
        <li><a href="#band">¿Como funciona?</a></li>
        <li><a href="#">Nosotros</a></li>
        <li class="submenu">
            <a href="#"><span class="fa fa-user"></span> {!!Auth::user()->name!!}</a>
            <ul class="children">
              <li><a href="{!!URL::to('/logout')!!}"><span class="fa fa-sign-out"></span>LogOut</a></li>
            </ul>
        </li>
      </ul>
  </nav>