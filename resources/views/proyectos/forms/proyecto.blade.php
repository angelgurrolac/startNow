<div class="form-group">
	{!!Form::label('nombre','Nombre:')!!}
	{!!Form::text('nombre',null,['class'=>'form-control', 'placeholder'=>'Ingresa el Nombre de tu proyecto'])!!}
</div>
<div class="form-group">
	{!!Form::label('Descripcion corta', 'Descripción corta (Max. 500)')!!}
	{!!Form::textarea('descCorta',null,['class'=>'form-control', 'placeholder'=>'Ingresa una pequeña descripcion', 'maxlength' => '500', 'size' =>'30x4'])!!}
</div>
<div class="form-group">
	{!!Form::label('Descripcion Larga','Descripción larga (Max. 2000):')!!}
	{!!Form::textarea('descLarga',null,['class'=>'form-control', 'placeholder'=>'Ingresa una descripcion', 'maxlength' => '2000',  'size' =>'30x6'])!!}
</div>
<div class="form-group">
	{!!Form::label('Imagen','Imagen:')!!}
	{!!Form::file('imagenUrl', ['accept' => 'image/*'])!!}
</div>
<div class="form-group">
	{!!Form::label('videoUrl','Url del video:')!!}
	{!!Form::text('videoUrl',null,['class'=>'form-control', 'placeholder'=>'http://youtube/example'])!!}
</div>
<div class="form-group">
	{!!Form::label('MetaMin','Meta minima ($):')!!}
	{!!Form::text('metaMin',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'Meta minima', 'id' => 'numbersonly'])!!}
</div>
<div class="form-group">
	{!!Form::label('MetaMax','Meta maxima ($):')!!}
	{!!Form::text('metaMax',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'Meta maxima' , 'id' => 'numbersonly2'])!!}
</div>
<div class="form-group">
	{!!Form::label('fechaInicio','Fecha de inicio:')!!}
	{!!Form::date('fechaInicio', \Carbon\Carbon::now(), ['class' => 'form-control'])!!}
</div>
<div class="form-group">
	{!!Form::label('fechaFin','Fecha de termino:')!!}
	{!!Form::date('fechaFin', \Carbon\Carbon::now(), ['class' => 'form-control'] )!!}
</div>
<div class="form-group">
	{!!Form::label('numeroClientes','Numero de clientes:')!!}
	{!!Form::number('numeroClientes',null,['class'=>'form-control', 'placeholder'=>'Cantidad de clientes'])!!}
</div>
<div class="form-group">
	{!!Form::label('inversion','Inversion:')!!}
	{!!Form::text('inversion',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'inversion al momento', 'id' => 'numbersonly3'])!!}

</div>
<div class="form-group">
	{!!Form::label('valorMercado','Valor en el mercado:')!!}
	{!!Form::text('valorMercado',null,['class'=>'form-control', 'placeholder'=>'Valor de mercado'])!!}
</div>
<div class="form-group">
	{!!Form::label('descComollegarClientes','Descripcion clientes:')!!}
	{!!Form::text('descComollegarClientes',null,['class'=>'form-control', 'placeholder'=>'Descripcion clientes'])!!}
</div>
<div class="form-group">
	{!!Form::label('propuestaValor','Propuesta de valor:')!!}
	{!!Form::text('propuestaValor',null,['class'=>'form-control', 'placeholder'=>'Propuesta de valor'])!!}
</div>


<div class="form-group">
	{!!Form::text('idUsuario',Auth::user()->id,['class'=>'form-control'])!!}
</div>
