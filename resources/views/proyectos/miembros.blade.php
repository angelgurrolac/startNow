@extends('layouts.admin')
	@include('alerts.request')
		@section('content')
		  	{!!Form::open(['route' => 'proyectos.store', 'method'=>'POST','files' => true])!!}
		  		@include('proyectos.forms.miembros')
				{!!Form::submit('Registrar',['class'=>'btn btn-primary'])!!}
			{!!Form::close()!!}
	@endsection