@extends('layouts.admin')

	
@include('alerts.success')
	@section('content')
	<div class="users">
		
		
		<table class="table">
			<thead>
				<th>Nombre</th>
				<th>Correo</th>
				<th>Estado</th>
				<th>Municipio</th>
			</thead>
			@foreach($users as $user)
				<tbody>
					<td>{{$user->usuario}}</td>
					<td>{{$user->email}}</td>
					<td>{{$user->estado}}</td>
					<td>{{$user->municipio}}</td>
					<td>
						{!!link_to_route('usuario.edit', $title = 'Editar', $parameters = $user->id, $attributes = ['class'=>'btn btn-primary'])!!}
						
					</td>
				</tbody>
			@endforeach
		</table>

	
		
	</div>
	@endsection
	@section('scripts')
		{!!Html::script('js/script3.js')!!}
	@endsection