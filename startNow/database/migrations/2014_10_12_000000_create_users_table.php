<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
        


            $table->string('name');
            $table->string('Apeido_P');
            $table->string('Apeido_M');
            $table->string('Direccion');
            $table->string('CP');
            $table->string('Numero_Ext');
            $table->string('Pais');
            $table->string('CD');
            $table->string('Numero_Cel');
            $table->string('Numero_Casa');
            $table->string('Sex');
            $table->string('Fecha');
            $table->string('perfil');
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
