<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlianzaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alianza', function (Blueprint $table) {
            $table->increments('idAlianza');
            $table->string('nombreAlianza',50);
            $table->string('descripcionAlianza',500);
            $table->string('urlAlianza',100);
            $table->string('urlImagenAlianza',100);
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
        Schema::dropIfExists('alianza');
    }
}
