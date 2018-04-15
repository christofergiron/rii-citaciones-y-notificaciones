<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenCapturaPersonaNaturalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_capturas_personas_naturales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_orden_captura');
            $table->integer('id_persona');
            //se tiene que cargar su lugar de vivienda
            //porque se le requerire, orden captura normal o para extradicion
            //0 para medidas cautelares, 1 por loquera del juez, 2 captura fin extradicion
            $table->boolean('deleted')->default(false);
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
        Schema::dropIfExists('ordenes_capturas_personas_naturales');
    }
}
