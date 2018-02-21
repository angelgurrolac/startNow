@extends('layouts.admin')
@include('alerts.success')
	<div class="proyectos">
		
		@section('content')
		<table class="table">
			<thead>
				<th>ID</th>
				<th class="col-sm-3">Nombre</th>
				<th class="col-sm-3">Imagen</th>
				<th class="col-sm-4">Descripcion Corta</th>
				<th class="col-sm-1"></th>
				<th></th>
			</thead>
			@foreach($proyectos as $proyecto)
				<tbody>
					<td>{{$proyecto->idProyecto}}</td>
					<td class="col-sm-3">{{$proyecto->nombre}}</td>
					<td class="col-sm-3"><img src="proyectosImg/{{$proyecto->imagenUrl}}" width="250px"></td>
					<td class="col-sm-4">{{$proyecto->descCorta}}</td>
					<td class="col-sm-1"></td>
					<td>
						{!!link_to_route('proyectos.edit', $title = 'Editar', $parameters = $proyecto->idProyecto, $attributes = ['class'=>'btn btn-primary'])!!}
					</td>
				</tbody>
			@endforeach
		</table>
			<center>{!!$proyectos->render()!!}</center>
	</div>
	@endsection
	@section('scripts')
		{!!Html::script('js/script3.js')!!}
	@endsection