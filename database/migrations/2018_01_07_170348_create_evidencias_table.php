<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_expediente');
            $table->string('tipo_evidencia');
            $table->string('embase');
            $table->string('descripcion');
            $table->string('fiscal');
            $table->integer('lugar_encuentro');
            $table->integer('lugar_almacenaje');
            $table->integer('oficial_encargado');
            $table->integer('placa_encargado');
            $table->DateTime('fecha_encuentro');
            //si se la encontrador a una persona
            $table->integer('id_persona')->nullable();
            $table->string('observaciones')->nullable();
            $table->DateTime('fecha_analisis')->nullable();
            $table->integer('lugar_analisis')->nullable();
            $table->integer('perito_analisis')->nullable();
            $table->DateTime('fecha_remision')->nullable();
            $table->integer('lugar_remision')->nullable();
            //no se si se almacenaria la foto de la evidencia?
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
        Schema::dropIfExists('evidencias');
    }
}
