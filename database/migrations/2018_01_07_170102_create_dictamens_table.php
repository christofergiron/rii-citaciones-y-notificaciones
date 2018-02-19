<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDictamensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dictamenes', function (Blueprint $table) {
          $table->increments('id');
          $table->string('dictamable_type')->nullable();
          $table->integer('dictamable_id')->nullable();
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
          $table->longText('descripcion')->nullable();
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
        Schema::dropIfExists('dictamenes');
    }
}
