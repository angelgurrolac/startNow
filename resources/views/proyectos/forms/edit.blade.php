@extends('layouts.admin')
	@section('content')
	@include('alerts.request')
	{!!Form::model($proyectos,['route'=>['proyectos.update',$proyectos->id],'method'=>'PUT'])!!}
		@include('proyectos.forms.edit')
	{!!Form::submit('Actualizar',['class'=>'btn btn-primary'])!!}
	{!!Form::close()!!}

    {!!Form::open(['route'=>['proyectos.destroy', $proyectos], 'method' => 'DELETE'])!!}
	{!!Form::submit('Eliminar',['class'=>'btn btn-danger'])!!}
	{!!Form::close()!!}


	@endsection