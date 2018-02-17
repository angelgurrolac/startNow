<?php

namespace startnow;
use Illuminate\Auth\Authenticatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class proyectos extends Model 
{
    protected $table="proyectos";

   protected $fillable = ['nombre', 'descCorta', 'descLarga','imagenUrl','videoUrl','metaMin','metaMax','fechaInicio','fechaFin', 'idProducto','idUsuario','idMercado','numeroClientes','inversion','valorMercado', 'descComollegarClientes', 'propuestaValor', 'idMiembro'];

   public function setImagenUrlAttribute($imagenUrl) {
   		$this->attributes['imagenUrl'] = Carbon::now()->second.$imagenUrl->getClientOriginalName();
   		$name = Carbon::now()->second.$imagenUrl->getClientOriginalName();
   		\Storage::disk('local')->put($name, \File::get($imagenUrl));
   }
    
}

