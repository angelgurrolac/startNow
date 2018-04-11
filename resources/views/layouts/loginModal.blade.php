<div class="modal fade" id="Mymodal" tabindex="-1" role="dialog" data-backdrop ="false" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      {{-- <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> --}}
      <div class="modal-body">
        <div class="row text-center">
          <div class="col-lg-6 iconosDiv">
              <i class="far fa-paper-plane"></i>
            </div>
            <div class="col-lg-6 iconosDiv">
              <i class="far fa-handshake"></i>
            </div>
        </div>
        <div class="row text-center botones">
          <div class="col-lg-6 text">
            <a href="proyectos/create">Emprender</a>
          </div>
          <div class="col-lg-6 text botones">
            <a href="{!!URL::to('/')!!}">Invertir</a>
          </div>
        </div>
        <div class="row text-center lblProy">
          <a href="{!!URL::to('todos')!!}"><span>VER PROYECTOS</span></a>
        </div>
      </div>
    </div>
  </div>
</div>


