<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudContraOrdensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_contra_ordenes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_orden_captura');
            $table->integer('id_expediente');
            $table->integer('id_persona');//llave foranea de imputado
            $table->integer('id_contra_orden')->nullable();
            $table->DateTime('fecha_aprovacion')->nullable();
            $table->DateTime('fecha_rechazo')->nullable();
            $table->string('razon_rechazo')->nullable();
            $table->string('workflow_state')->nullable();
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
        Schema::dropIfExists('solicitudes_contra_ordenes');
    }
}
