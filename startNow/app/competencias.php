<?php

namespace startnow;

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
}
