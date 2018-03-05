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
				<th>Nombre</th>
				<th>Apellido Paterno</th>
				<th>Apellido Materno</th>
				<th>LinkedIn</th>
				<th>Fotografia</th>
				<th>Puesto</th>
				<th>Descripción</th>
			</tr>
			@foreach($miembros as $miembro)
				<tr>
					<td>{{$miembro -> nombres}}</td>
					<td>{{$miembro -> apellidoP}}</td>
					<td>{{$miembro -> apellidoM}}</td>
					<td>{{$miembro -> urlPerfil}}</td>
					<td><img src="proyectosImg/{{$miembro -> imagenUrl}}"></td>
					<td>{{$miembro -> puesto}}</td>
					<td>{{$miembro -> descripcion}}</td>
				</tr>
			@endforeach
		</table>
		<h3>Agegar Miembro</h3>
		  	{!!Form::open(['route' => 'miembros.store', 'method'=>'POST','files' => true])!!}
		  		@include('proyectos.forms.miembroF')
				{!!Form::submit('Registrar',['class'=>'btn btn-primary'])!!}
				<button id="adicional" name="adicional" type="button" class="btn btn-warning" >Mas</button>
				<a href="{{URL::to('proyectos')}}" class="btn btn-success">Listo</a>
			{!!Form::close()!!}
				
	@endsection