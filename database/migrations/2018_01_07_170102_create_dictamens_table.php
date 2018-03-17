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
          $table->integer('id_autor');
          //no se si esto va relacionado con la solicitud?
          $table->integer('id_expediente');
          $table->DateTime('fecha_creacion');
          //el fiscal va amarrado al expediente
          //$table->integer('id_fiscal')->nullable();
          $table->integer('unidad');
          //este id de solicitud va en dictamen vehicular
          $table->string('descripcion')->nullable();
          $table->string('observaciones')->nullable();
          //a quien envia en dictamen,no tendria que ir en la solicitud dictamen
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
