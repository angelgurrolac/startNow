<div class="form-group">
	{!!Form::label('nombre','Nombre:')!!}
	{!!Form::text('nombre',null,['class'=>'form-control', 'placeholder'=>'Ingresa el Nombre de tu proyecto'])!!}
</div>
<div class="form-group">
	{!!Form::label('Descripcion corta', 'Descripción corta (Max. 100)')!!}
	{!!Form::text('descCorta',null,['class'=>'form-control', 'placeholder'=>'Ingresa una pequeña descripcion'])!!}
</div>
<div class="form-group">
	{!!Form::label('Descripcion Larga','Descripción larga (Max. 200):')!!}
	{!!Form::text('descLarga',null,['class'=>'form-control', 'placeholder'=>'Ingresa una descripcion'])!!}
</div>
<div class="form-group">
	{!!Form::label('Imagen','Imagen:')!!}
	{!!Form::file('imagenUrl')!!}
</div>
<div class="form-group">
	{!!Form::label('videoUrl','Url del video:')!!}
	{!!Form::text('videoUrl',null,['class'=>'form-control', 'placeholder'=>'http://youtube/example'])!!}
</div>
<div class="form-group">
	{!!Form::label('MetaMin','Meta minima ($):')!!}
	{!!Form::text('metaMin',null,['class'=>'form-control', 'placeholder'=>'Meta minima'])!!}
</div>
<div class="form-group">
	{!!Form::label('MetaMax','Meta maxima ($):')!!}
	{!!Form::text('metaMax',null,['class'=>'form-control', 'placeholder'=>'Ingresa una descripcion'])!!}
</div>
<div class="form-group">
	{!!Form::label('fechaInicio','Fecha de inicio:')!!}
	{!!Form::text('fechaInicio',null,['class'=>'form-control', 'placeholder'=>'dd/mm/yyyy'])!!}
</div>
<div class="form-group">
	{!!Form::label('fechaFin','Fecha de termino:')!!}
	{!!Form::text('fechaFin',null,['class'=>'form-control', 'placeholder'=>'dd/mm/yyyy'])!!}
</div>
<div class="form-group">
	{!!Form::label('numeroClientes','Numero de clientes:')!!}
	{!!Form::text('numeroClientes',null,['class'=>'form-control', 'placeholder'=>'Cantidad de clientes'])!!}
</div>
<div class="form-group">
	{!!Form::label('inversion','Inversion:')!!}
	{!!Form::text('inversion',null,['class'=>'form-control', 'placeholder'=>'inversion'])!!}
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
	
</div>
