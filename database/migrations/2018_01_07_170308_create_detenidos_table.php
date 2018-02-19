<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetenidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detenidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_orden')->nullable();
            $table->integer('id_expediente')->nullable();
            $table->string('nombre');
            $table->DateTime('fecha_nacimiento')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('sexo')->nullable();
            $table->integer('edad')->nullable();
            $table->DateTime('fecha_captura');
            $table->integer('lugar_captura');
            $table->integer('lugar_retencion');
            $table->string('fiscal');
            $table->DateTime('fecha_remision_mp')->nullable();
            $table->string('fiscalia')->nullable();
            $table->DateTime('fecha_remision_pj')->nullable();
            $table->string('juzgado')->nullable();
            $table->DateTime('fecha_remision_penal')->nullable();
            $table->string('nombre_penal')->nullable();
            $table->DateTime('fecha_extracion')->nullable();
            $table->string('pais_extraditado')->nullable();
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
        Schema::dropIfExists('detenidos');
    }
}
