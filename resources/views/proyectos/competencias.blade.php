@extends('layouts.admin')
	@include('alerts.request')
		@section('content')
		<script>
			$(function() {
				$('#adicional').on('click', function() {
					$('#tabla tbody tr:eq(0)').clone().removeClass('fila-fija').appendTo('#tabla')
				});
			});
		</script>
		<table class="table col-sm-12 table-striped">
			<tr>
				<th>Competencia</th>
				<th>Descripcion de la competencia</th>
				<th>Imagen de la competencia</th>
				<th>Id proyecto</th>
			
			</tr>
			@foreach($competencias as $competencia)
				<tr>
					<td>{{$competencia -> nombreCompetencia}}</td>
					<td>{{$competencia -> descripcionCompetencia}}</td>
					
					<td><img src="proyectosImg/{{$competencia -> urlImagenCompetencia}}" style="width: 30%;"></td>
					<td>{{$competencia -> idProyecto}}</td>
					<td>
						{!!Form::open(['route'=>['competencias.destroy', $competencia-> idCompetencia], 'method' => 'DELETE'])!!}
							{!!Form::submit('Eliminar',['class'=>'btn btn-danger'])!!}
						{!!Form::close()!!}
					</td>
					
				</tr>
			@endforeach
		</table>
		<h3>Agegar Competencia</h3>
		  	{!!Form::open(['route' => 'competencias.store', 'method'=>'POST','files' => true])!!}
		  		@include('proyectos.forms.competenciasF')
				{!!Form::submit('Registrar',['class'=>'btn btn-primary'])!!}
				<button id="adicional" name="adicional" type="button" class="btn btn-warning" >Mas</button>
				<a href="{{URL::to('proyectos')}}" class="btn btn-success">Listo</a>
			{!!Form::close()!!}
				
	@endsection