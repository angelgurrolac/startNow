

@extends('layouts.admin')
	@include('alerts.request')
		@section('content')
		  	{!!Form::open(['route' => 'proyectos.store', 'method'=>'POST','files' => true])!!}
		  		@include('proyectos.forms.proyecto')
				{!!Form::submit('Siguiente',['class'=>'btn btn-primary'])!!}
				
			{!!Form::close()!!}
	@endsection