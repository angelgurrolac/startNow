<?php
namespace startnow;

use Illuminate\Database\Eloquent\Model;

class informacion extends Model
{
    protected $table="proyectos";

    public function scopeConsulta() {

    	$proyectos = DB::table('proyectos')
    	->select('nombre')
    	->get();

    	return $proyectos;
    }
    
}
