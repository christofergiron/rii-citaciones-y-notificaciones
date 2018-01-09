<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
          $table->increments('id');
          $table->string('titulo');
          //$table->string('tipo_reporte');
          $table->integer('id_tipo_reporte');
          $table->integer('id_autor');
          //$table->string('autor');
          $table->integer('placa_autor')->nullable();
          $table->string('id_expediente')->nullable();
          $table->DateTime('fecha_creacion');
          $table->string('Fiscal')->nullable();
          $table->string('depto_policia_id');
          //$table->integer('depto_policial');
          $table->DateTime('fecha_solicitud_reporte')->nullable();
          $table->string('descripcion')->nullable();
          $table->string('observaciones')->nullable();
          $table->integer('remitido_A');
          $table->DateTime('fecha_envio');
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
        Schema::dropIfExists('reportes');
    }
}
