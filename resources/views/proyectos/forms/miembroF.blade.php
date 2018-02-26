<div class="form-group">
	{!!Form::label('nombres','Nombres:')!!}
	{!!Form::text('nombres',null,['class'=>'form-control', 'placeholder'=>'Ingresa el Nombre de tu proyecto'])!!}
</div>
<div class="form-group">
	{!!Form::label('apellidoP ', 'Apellido Paterno (Max. 500)')!!}
	{!!Form::text('apellidoP',null,['class'=>'form-control', 'placeholder'=>'Ingresa una pequeña descripcion', 'maxlength' => '500', 'size' =>'30x4'])!!}
</div>
<div class="form-group">
	{!!Form::label('apellidoM ', 'Apellido Materno (Max. 500)')!!}
	{!!Form::text('apellidoM',null,['class'=>'form-control', 'placeholder'=>'Ingresa una pequeña descripcion', 'maxlength' => '500', 'size' =>'30x4'])!!}
</div>
<div class="form-group">
	{!!Form::label('urlPerfil','LinkedIn:')!!}
	{!!Form::text('urlPerfil',null,['class'=>'form-control', 'placeholder'=>'http://youtube/example'])!!}
</div>
<div class="form-group">
	{!!Form::label('imagenUrl','Fotografia:')!!}
	{!!Form::file('imagenUrl', ['accept' => 'image/*'])!!}
</div>

<div class="form-group">
	{!!Form::label('puesto','Puesto Desempeñado :')!!}
	{!!Form::text('puesto',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'Meta minima', 'id' => 'numbersonly'])!!}
</div>
<div class="form-group">
	{!!Form::label('descripcion','Descripcion del puesto:')!!}
	{!!Form::text('descripcion',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'Meta maxima' , 'id' => 'numbersonly2'])!!}
</div>


<div class="form-group">
	{!!Form::text('idProyecto',Auth::proyectos()->id,['class'=>'form-control'])!!}
</div>
