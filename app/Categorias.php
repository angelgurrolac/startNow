<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $table = 'categorias';
 
    protected $fillable = ['name'];
}
