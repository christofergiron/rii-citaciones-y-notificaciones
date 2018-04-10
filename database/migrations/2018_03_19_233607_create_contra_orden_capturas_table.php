<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContraOrdenCapturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contra_ordenes_capturas', function (Blueprint $table) {
            $table->increments('id');
            //si no es solicitud ocuparia los datos que solicitud tiene
            $table->integer('id_orden');
            $table->DateTime('fecha_creacion');
            $table->integer('id_expediente');
            $table->string('razon');
            $table->integer('id_juez')->nullable();
            $table->integer('id_fiscal')->nullable();
            $table->string('descripcion')->nullable();
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
        Schema::dropIfExists('contra_ordenes_capturas');
    }
}
