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
            $table->integer('id_orden_captura')->nullable();
            $table->integer('id_expediente')->nullable();
            $table->string('nombre');
            $table->DateTime('fecha_nacimiento')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('sexo')->nullable();
            $table->integer('edad')->nullable();
            $table->DateTime('fecha_captura');
            $table->integer('lugar_captura');
            $table->string('fiscal');
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
