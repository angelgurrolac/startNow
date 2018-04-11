<div class="row">
	<div class="form-group col-lg-8">
		{!!Form::label('nombre','Nombre:')!!}
		{!!Form::text('nombre',null,['class'=>'form-control', 'placeholder'=>'Ingresa el Nombre de tu proyecto'])!!}
	</div>
	<div class="form-group col-lg-4">
		{!!Form::label('categoria','Categoria:')!!}
		{!!Form::select('categoria', $categorias, null, ['placeholder' => 'Selecciona categoria', 'class' => 'form-control'])!!}
	</div>
</div>
<div class="row">
	<div class="form-group col-lg-6">
	{!!Form::label('Descripcion corta', 'Introducción (Max. 200)')!!}
	{!!Form::textarea('descCorta',null,['class'=>'form-control', 'placeholder'=>'Ingresa una introducción sobre tu proyecto', 'style'=> 'resize: none;', 'maxlength' => '200', 'size' =>'30x6'])!!}
</div>
<div class="form-group col-lg-6">
	{!!Form::label('Descripcion Larga','Descripción larga (Max. 2000):')!!}
	{!!Form::textarea('descLarga',null,['class'=>'form-control', 'placeholder'=>'Ingresa una descripcion', 'style'=> 'resize: none;', 'maxlength' => '2000',  'size' =>'30x6'])!!}
</div>
</div>
<div class="row">
	<div class="form-group col-lg-6">
		{!!Form::label('Imagen','Imagen:')!!}
		{!!Form::file('imagenUrl', ['accept' => 'image/*',  'class' => 'form-control'])!!}
	</div>
	<div class="form-group col-lg-6">
		{!!Form::label('videoUrl','Url del video:')!!}
		{!!Form::text('videoUrl',null,['class'=>'form-control', 'placeholder'=>'http://youtube/example'])!!}
	</div>
</div>
<div class="row">
	<div class="col-lg-6 noMargen">
		<div class="form-group col-lg-6">
			{!!Form::label('MetaMin','Meta minima ($):')!!}
			{!!Form::text('metaMin',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'Meta minima', 'id' => 'numbersonly'])!!}
		</div>
		<div class="form-group col-lg-6">
			{!!Form::label('MetaMax','Meta maxima ($):')!!}
			{!!Form::text('metaMax',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'Meta maxima' , 'id' => 'numbersonly2'])!!}
		</div>
	</div>
	<div class="col-lg-6 noMargen">
		<div class="form-group col-lg-6">
			{!!Form::label('fechaInicio','Fecha de inicio:')!!}
			{!!Form::date('fechaInicio', \Carbon\Carbon::now(), ['class' => 'form-control'])!!}
		</div>
		<div class="form-group col-lg-6">
			{!!Form::label('fechaFin','Fecha de termino:')!!}
			{!!Form::date('fechaFin', \Carbon\Carbon::now(), ['class' => 'form-control'] )!!}
		</div>
	</div>
</div>
<div class="row">
	<div class="form-group col-lg-6">
		{!!Form::label('numeroClientes','Numero de clientes:')!!}
		{!!Form::number('numeroClientes',null,['class'=>'form-control', 'placeholder'=>'Cantidad de clientes'])!!}
	</div>
	<div class="form-group col-lg-6">
		{!!Form::label('inversion','Inversion:')!!}
		{!!Form::text('inversion',null,['class'=>'form-control currency numbersonly', 'placeholder'=>'inversion al momento', 'id' => 'numbersonly3'])!!}

	</div>
</div>
<div class="row">
	<div class="form-group col-lg-6">
		{!!Form::label('valorMercado','Valor en el mercado:')!!}
		{!!Form::textarea('valorMercado',null,['class'=>'form-control', 'placeholder'=>'Valor de mercado','style'=> 'resize: none;', 'maxlength' => '200', 'size' =>'30x4'])!!}
	</div>
	<div class="form-group col-lg-6">
		{!!Form::label('descComollegarClientes','Descripcion clientes:')!!}
		{!!Form::textarea('descComollegarClientes',null,['class'=>'form-control', 'placeholder'=>'Descripcion clientes','style'=> 'resize: none;', 'maxlength' => '200', 'size' =>'30x4'])!!}
	</div>
</div>

<div class="row">
	<div class="form-group col-lg-12">
		{!!Form::label('propuestaValor','Propuesta de valor:')!!}
		{!!Form::textarea('propuestaValor',null,['class'=>'form-control', 'placeholder'=>'Propuesta de valor','style'=> 'resize: none;', 'maxlength' => '200', 'size' =>'30x3'])!!}
	</div>
</div>
{!!Form::hidden('idUsuario',Auth::user()->id,['class'=>'form-control'])!!}
<style>
	.noMargen {
		padding: 0;
	}
	.well{
		width: 684px !important;
	    padding-left: 20px;
	    margin-right: 12px;
	    margin-left: 15px;
	}
</style>