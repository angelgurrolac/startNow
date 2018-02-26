<?php

namespace startnow;

use Illuminate\Database\Eloquent\Model;

class Miembros extends Model
{
      
 protected $table = 'miembrosEquipo';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombres', 'apellidoP', 'apellidoM','urlPerfil','idProyecto','imagenUrl','puesto','descripcion'];

           
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    }





