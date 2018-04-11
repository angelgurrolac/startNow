<table class="table col-sm-12 bg-faded" id="tabla">
		<tr class="fila-fija">
			<td>{!!Form::text('nombres[]',null,['class'=>'form-control', 'placeholder'=>'Nombre'])!!}</td>
			<td>{!!Form::text('apellidoP[]',null,['class'=>'form-control ', 'placeholder'=>'Apellido Paterno'])!!}</td>
			<td>{!!Form::text('apellidoM[]',null,['class'=>'form-control ', 'placeholder'=>'Apellido Materno'])!!}</td>
			<td>{!!Form::text('urlPerfil[]',null,['class'=>'form-control ', 'placeholder'=>'http://...'])!!}</td>
			<td>{!!Form::file('imagenMiembro[]', ['accept' => 'image/*', 'class' => 'form-control ', 'id' => 'exampleFormControlFile1'])!!}</td>
			<td>{!!Form::text('puesto[]',null,['class'=>'form-control ' , 'placeholder'=>'Puesto'])!!}</td>
			<td>{!!Form::text('descripcion[]',null,['class'=>'form-control ', 'placeholder'=>'Descripcion'])!!}</td>
		</tr>

</table>
