<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_expediente')->nullable();
            $table->integer('id_flagrancia')->nullable();
            $table->integer('id_registro_personal')->nullable();
            $table->integer('id_denuncia')->nullable();
            $table->integer('id_allanamiento')->nullable();
            $table->integer('id_captura')->nullable();
            $table->integer('id_escena_delito')->nullable();

            $table->string('tipo_evidencia');
            $table->string('embase');
            $table->string('descripcion');
            $table->string('fiscal');
            $table->integer('lugar_encuentro');
            $table->integer('lugar_almacenaje');
            $table->integer('oficial_encargado');
            $table->integer('placa_encargado');
            $table->DateTime('fecha_encuentro');
            $table->integer('id_funcionario')->nullable();
            //nuevos datos
            $table->string('embalaje'); //esto que es
            $table->string('rotulaje');
            $table->integer('numero_caso');
            $table->integer('numero_noticia_criminal'); //sera un objeto?
            $table->string('cadena_custodia'); //esto que es
            //si se la encontrador a una persona
            $table->integer('id_persona')->nullable();
            $table->string('observaciones')->nullable();
            $table->DateTime('fecha_analisis')->nullable();
            $table->integer('lugar_analisis')->nullable();
            $table->integer('perito_analisis')->nullable();

            $table->DateTime('fecha_remision')->nullable();
            $table->integer('lugar_remision')->nullable();
            $table->DateTime('fecha_traslado')->nullable();
            $table->integer('lugar_traslado')->nullable();

            $table->integer('id_solicitud')->nullable();
            $table->string('tipo_analisis')->nullable();
            $table->integer('id_analisis')->nullable();
            $table->integer('id_laboratorio_analisis')->nullable();
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
        Schema::dropIfExists('evidencias');
    }
}
