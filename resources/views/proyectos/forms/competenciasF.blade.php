<table class="table col-sm-12 bg-faded" id="tabla">
		<tr class="fila-fija">
			<td>{!!Form::text('idProyecto[]', $proyectos, ['class' => 'form-control'])!!}</td>
			<td>{!!Form::text('nombreCompetencia[]',null,['class'=>'form-control', 'placeholder'=>'Nombre de la competencia'])!!}</td>
			<td>{!!Form::text('descripcionCompetencia[]',null,['class'=>'form-control', 'placeholder'=>'Descripción'])!!}</td>
			<td>{!!Form::file('urlImagenCompetencia[]', ['accept' => 'image/*', 'class' => 'form-control', 'id' => 'exampleFormControlFile1'])!!}</td>
		</tr>

</table>
