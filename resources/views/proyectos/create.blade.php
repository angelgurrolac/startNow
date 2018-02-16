@extends('layouts.admin')
	@section('content')
		  	{!!Form::open(['', 'method'=>'POST','files' => true])!!}
		  		@include('proyectos.forms.proyecto')
				{!!Form::submit('Registrar',['class'=>'btn btn-primary'])!!}
			{!!Form::close()!!}
	@endsection