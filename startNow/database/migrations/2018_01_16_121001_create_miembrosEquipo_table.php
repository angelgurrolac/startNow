<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiembrosEquipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miembrosEquipo', function (Blueprint $table) {
            $table->increments('idMiembro');
            $table->string('nombres',100);
            $table->string('apellidoM',50);
            $table->string('apellidoP',50);
            $table->string('urlPerfil',100);
            $table->integer('idProyecto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('miembrosEquipo');
    }
}
