<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
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
            $table->integer('id_resolucion')->nullable();
            //notificacion
            $table->LongText('asunto');
            $table->string('objeto_proceso');//este es el objeto del proceso de las notificaciones
            $table->string('observaciones')->nullable();
            //SUJETOS PROCESALES
            $table->integer('id_juez');
            $table->integer('id_fiscal');
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
        Schema::dropIfExists('notificaciones');
    }
}
