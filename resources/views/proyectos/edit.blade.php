@extends('layouts.admin')
	@section('content')
		  	{!!Form::model($proyecto,['route' => ['proyectos.update',$proyecto], 'method'=>'PUT','files' => true])!!}
		  		@include('proyectos.forms.proyecto')
				{!!Form::submit('Actualizar',['class'=>'btn btn-primary'])!!}
			{!!Form::close()!!}
<br>
			{!!Form::open(['route'=>['proyectos.destroy', $proyecto], 'method' => 'DELETE'])!!}
			{!!Form::submit('Eliminar',['class'=>'btn btn-danger'])!!}
			{!!Form::close()!!}
	@endsection