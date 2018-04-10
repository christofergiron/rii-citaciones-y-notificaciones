<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenCapturaEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //cambio, pensa un poco mas en el trato de estos cambios, por ahora no la utilices
        Schema::create('orden_captura_estados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_orden_captura');
            $table->integer('id_contra_orden')->nullable();
            $table->integer('id_funcionario');
            $table->string('estado_antiguo')->nullable();
            $table->string('estado_nuevo');
            $table->DateTime('fecha');
            $table->string('motivo');
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
        Schema::dropIfExists('orden_captura_estados');
    }
}
