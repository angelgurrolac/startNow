<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Miembros extends Model
{
      
 protected $table = 'miembrosEquipo';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombres', 'apellidoP', 'apellidoM','urlPerfil','idProyecto','imagenUrl','puesto','descripcion'];

    public function setImagenUrlAttribute($imagenUrl) { 
       $this->attributes['imagenUrl'] = Carbon::now()->second.$imagenUrl->getClientOriginalName(); 
       $name = Carbon::now()->second.$imagenUrl->getClientOriginalName(); 
       \Storage::disk('local')->put($name, \File::get($imagenUrl)); 
   } 
}   
  




