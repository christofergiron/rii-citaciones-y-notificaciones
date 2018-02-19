<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capturas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('capturable_type')->nullable();
            $table->integer('capturable_id')->nullable();
            $table->string('workflow_state')->nullable();
            $table->integer('id_orden')->nullable();
            $table->integer('id_requerimiento')->nullable();
            $table->integer('id_expediente')->nullable();
            $table->integer('id_persona');
            $table->integer('id_lugar');
            $table->integer('id_funcionario');
            $table->string('descripcion_captura');
            $table->string('observaciones');
            $table->DateTime('fecha_captura');
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
        Schema::dropIfExists('capturas');
    }
}
