<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCanalEnvioCNsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canales_envio', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_citacion')->nullable();
            $table->integer('id_notificacion')->nullable();
            $table->integer('id_emplazamiento')->nullable();
            $table->integer('id_requerimiento')->nullable();
            //si es en la tabla de envios, el usuario tiene que darle la opcion: "mostrar en tabla de avisos"
            $table->integer('id_funcionario');
            //COMUNICACION
            //la lista de posibles formas de comunicacion
            $table->string('canal_envio');
            //descripcion de a donde se comunico
            $table->LongText('medios_envio');
            $table->string('observaciones')->nullable();
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
        Schema::dropIfExists('canales_envio');
    }
}
