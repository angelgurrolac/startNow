<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {

            $table->increments('idProyecto');
            $table->string('nombre',100);
            $table->integer('idUsuario');
            $table->string('descCorta',200);
            $table->text('descLarga');
            $table->string('imagenUrl',500);
            $table->string('videoUrl',100);
            $table->double('metaMin',8,2);
            $table->double('metaMax', 8, 2);
            $table->dateTime('fechaInicio');
            $table->dateTime('fechaFin');
            $table->double('numeroClientes',8,2);
            $table->binary('valorMercado');
            $table->binary('descComoLlegarClientes');
            $table->binary('propuestaValor');
            $table->boolean('inversion');
            $table->timestamps();
            $table->string('estatus');

            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('Proyectos');   //
    }
}
