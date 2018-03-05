<?php

namespace startnow;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class competencias extends Model
{
    protected $table="competencias";

    public function scopeConsulta() {

    	$competencias = DB::table('competencias')
    	->select('nombreCompetencia')
    	->get();

    	return $competencias;
    }
    protected $fillable = ['idCompetencia','nombreCompetencia','descripcionCompetencia','urlImagenCompetencia','idProyecto'];

    public function setImagenUrlAttribute($urlImagenCompetencia) { 
       $this->attributes['urlImagenCompetencia'] = Carbon::now()->second.$urlImagenCompetencia->getClientOriginalName(); 
       $name = Carbon::now()->second.$urlImagenCompetencia->getClientOriginalName(); 
       \Storage::disk('local')->put($name, \File::get($urlImagenCompetencia)); 
   } 

}
