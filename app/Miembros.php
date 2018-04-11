<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Miembros extends Model
{
      
 protected $table = 'miembrosEquipo';
 protected $primaryKey = 'idMiembro';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombres', 'apellidoP', 'apellidoM','urlPerfil','idProyecto','imagenMiembro','puesto','descripcion'];

    public function setimagenMiembroAttribute($imagenMiembro) {
       $this->attributes['imagenMiembro'] = Carbon::now()->second.$imagenMiembro->getClientOriginalName(); 
       $name = Carbon::now()->second.$imagenMiembro->getClientOriginalName(); 
       \Storage::disk('local')->put($name, \File::get($imagenMiembro)); 
   } 
}   
  




