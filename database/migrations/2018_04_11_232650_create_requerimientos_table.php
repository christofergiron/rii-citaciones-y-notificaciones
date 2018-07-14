<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequerimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requerimientos', function (Blueprint $table) {
          $table->increments('id');
          //ENCABEZADO
          $table->integer('id_expediente');
          $table->integer('id_funcionario');
          //no se si dejar esta integer o string, para referenci a la dependencia
          $table->string('organo_juridiccional');
          $table->DateTime('fecha_creacion');
          $table->integer('audiencia')->nullable();
          $table->integer('etapa')->nullable();
          //dejar la etapa y audiencia como string o esta llave foranea de proceso judicial
          $table->integer('proceso_judicial')->nullable();
          //$table->LongText('motivo'); //me suena a asunto
          //CITACION
          $table->string('parte_solicitante');//la persona que solicito la citacion,
          $table->integer('tipo_parte_solicitante');
          $table->LongText('asunto');
          $table->string('tipo_acto_procesal');//este es el objeto citacion
          $table->string('lugar_citacion');
          $table->DateTime('fecha_citacion');
          $table->string('observaciones')->nullable();
          //CITADO
          $table->string('persona_natural')->nullable();
          //que clase de persona esla citada: victima, testigo, etc
          $table->integer('tipo');
          $table->string('persona_juridica')->nullable();
          $table->tinyInteger('notificado');
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
        Schema::dropIfExists('requerimientos');
    }
}
