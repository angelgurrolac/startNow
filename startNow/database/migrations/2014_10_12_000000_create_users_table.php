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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombres',100);
            $table->string('apellidoM',50);
            $table->string('apellidoP',50);
            $table->string('calle',100);
            $table->integer('numeroExterior');
            $table->integer('codigoPostal');
            $table->string('ciudad',50);
            $table->string('numeroCelular',14);
            $table->integer('numeroCasa');
            $table->enum('sexo', ['masculino','femenino']);
            $table->date('fechaNacimiento');
            $table->string('password',200);
            $table->string('cPassword',200);
            $table->integer('idPerfil');
            $table->string('correoElectronico',100)->unique();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
