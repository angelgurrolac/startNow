<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {

            $table->increments('idProducto');
            $table->string('nombreProducto',100);
            $table->string('descripcionProducto',2000);
            $table->integer('idEtapa');
            $table->string('descAFondo',1000);
            $table->string('videoUrl',200);
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
        Schema::dropIfExists('productos');
    }
}
