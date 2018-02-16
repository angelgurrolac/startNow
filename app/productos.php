<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class productos extends Model
{

   protected $table="productos";

   public function scopeConsulta(){
   $mercados = DB::table('productos')
   ->select('nombreProducto')
   ->get();
   return  $productos;

	}
}
