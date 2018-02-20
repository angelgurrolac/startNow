@extends('layouts.admin')
	@section('content')
		  	{!!Form::model($proyecto,['route' => ['proyectos.update',$proyecto->id], 'method'=>'PUT','files' => true])!!}
		  		@include('proyectos.forms.proyecto')
				{!!Form::submit('Actualizar',['class'=>'btn btn-primary'])!!}
			{!!Form::close()!!}

			{!!Form::open(['route'=>['proyectos.destroy', $proyecto], 'method' => 'DELETE'])!!}
			{!!Form::submit('Eliminar',['class'=>'btn btn-danger'])!!}
			{!!Form::close()!!}
	@endsection