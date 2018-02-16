<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class equipo extends Model
{
protected $table="miembrosequipo";

    public function scopeConsulta() {

    	$miembrosequipo = DB::table('miembrosequipo')
    	->select('nombres')
    	->get();

    	return $miembrosequipo;
    }
}
