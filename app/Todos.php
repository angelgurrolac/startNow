<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class Todos extends Model
{
    {
    protected $table="proyectos";

    public function scopeConsulta() {

    	$proyecto = DB::table('proyectos')
    	->select('nombre')
    	->get();

    	return $proyecto;
    }
    
}

}
