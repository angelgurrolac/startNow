<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class etapas extends Model{

	

   protected $table="etapa";

   public function scopeConsulta(){
   $mercados = DB::table('etapa')
   ->select('nombreEtapa')
   ->get();
   return  $etapas;

	}
}
