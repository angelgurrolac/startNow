		<script>
			$(function() {
				$('#adicional').on('click', function() {
					$('#tabla tbody tr:eq(0)').clone().removeClass('fila-fija').appendTo('#tabla');

				});
			});
		</script>
		
		<div class="row">
			<h3 class="col-lg-3">Agegar Miembros</h3>
			<button id="adicional" name="adicional" type="button" class="btn btn-warning col-lg-offset-8 col-lg-1" style="margin-top: 15px" >Añadir mas +</button>
		</div>
  		@include('proyectos.forms.miembroF')
		