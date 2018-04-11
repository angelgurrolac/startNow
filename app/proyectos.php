<?php

namespace startnow;
use Illuminate\Auth\Authenticatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class proyectos extends Model 
{
    protected $table="proyectos";
    protected $primaryKey = 'idProyecto';

    protected $fillable = ['nombre', 'idUsuario', 'descCorta', 'descLarga','imagenUrl','videoUrl','metaMin','metaMax','fechaInicio','fechaFin','numeroClientes','inversion','valorMercado', 'descComollegarClientes', 'propuestaValor', 'estatus', 'categoria'];
 
   public function setImagenUrlAttribute($imagenUrl) { 
       $this->attributes['imagenUrl'] = Carbon::now()->second.$imagenUrl->getClientOriginalName(); 
       $name = Carbon::now()->second.$imagenUrl->getClientOriginalName(); 
       \Storage::disk('local')->put($name, \File::get($imagenUrl)); 
   } 
}