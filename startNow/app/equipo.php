<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class equipo extends Model
{
protected $table="miembrosEquipo";

    public function scopeConsulta() {

    	$miembrosEquipo = DB::table('miembrosEquipo')
    	->select('nombres')
    	->get();

    	return $miembrosEquipo;
    }
}
