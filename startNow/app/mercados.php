<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class mercados extends Model
{
   protected $table="mercado";

   public function scopeConsulta(){
   $mercados = DB::table('mercado')
   ->select('nombreMercado')
   ->get();
   return  $mercados;

	}
}