<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class alianzas extends Model
{
    
   protected $table="alianzas";

   public function scopeConsulta(){
   $alianzas = DB::table('alianzas')
   ->select('nombreAlianza')
   ->get();
   return  $alianzas;

	}
}
