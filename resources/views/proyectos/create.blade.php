@extends('layouts.admin')
	@section('content')
		  	{!!Form::open(['route' => 'proyectos.store', 'method'=>'POST','files' => true])!!}
		  		@include('proyectos.forms.proyecto')
				{!!Form::submit('Registrar',['class'=>'btn btn-primary'])!!}
			{!!Form::close()!!}
	@endsection