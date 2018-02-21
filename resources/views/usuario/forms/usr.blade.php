<div class="form-group">
		{!!Form::label('nombre','Nombre:')!!}
		{!!Form::text('name',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>
<div class="form-group">
		{!!Form::label('email','Correo:')!!}
		{!! Form::email('email', null, ['class' => 'form-control' , 'required' => 'required']) !!}
	</div>

<div class="form-group">
		{!!Form::label('Apeido_P','Apellido Paterno:')!!}
		{!!Form::text('Apeido_P',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>

<div class="form-group">
		{!!Form::label('Apeido_M','Apellido Materno:')!!}
		{!!Form::text('Apeido_M',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>

<div class="form-group">
		{!!Form::label('Direccion','Direccion:')!!}
		{!!Form::text('Direccion',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>

	

<div class="form-group">
		{!!Form::label('CP','Codigo Postal:')!!}
		{!!Form::number('CP',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>


<div class="form-group">
		{!!Form::label('Pais','Pais :')!!}
		{!!Form::text('Pais',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>

<div class="form-group">
		{!!Form::label('CD','Ciudad:')!!}
		{!!Form::text('CD',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>

	<div class="form-group">
		{!!Form::label('Numero_ext','Numero Exterior:')!!}
		{!!Form::number('Numero_Ext',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>

<div class="form-group">
		{!!Form::label('Numero_cel','Numero Celular:')!!}
		{!!Form::number('Numero_Cel',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}

		
	</div>

<div class="form-group">
		{!!Form::label('Numero_casa','Numero de casa:')!!}
		{!!Form::number('Numero_Casa',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>
<div class="form-group">
		{!!Form::label('Sex','Sexo:')!!}
		{!!Form::text('Sex',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>
<div class="form-group">
		{!!Form::label('Fecha','Fecha:')!!}
		{!!Form::date('fechaInicio', \Carbon\Carbon::now(), ['class' => 'form-control'])!!}
	</div>

	<div class="form-group">
		{!!Form::label('Perfil','Perfil:')!!}
		{!!Form::text('Perfil',null,['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>
	<div class="form-group">
		{!!Form::label('password','Contraseña:')!!}
		{!!Form::password('password',['class'=>'form-control','placeholder'=>'Ingresa el Nombre del usuario'])!!}
	</div>