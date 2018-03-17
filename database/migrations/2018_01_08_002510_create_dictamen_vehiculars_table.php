<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDictamenVehicularsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dictamen_vehiculares', function (Blueprint $table) {
            $table->increments('id');
            //este workflow estate no tiene que ir aqui, pero no se adonde
            $table->string('workflow_state')->nullable();
            //solo tendria que llevar el id vehiculo
            //tendria que ser una tabla n a n? victamenes vehiculares x vehiculos o con solicitudes?
            //y el id de dictamen vehicular tendria que ir en la tabla n a n
            $table->integer('id_solicitud');
            $table->string('informe_adjunto')->nullable();
            $table->string('informe_html')->nullable();
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
        Schema::dropIfExists('dictamen_vehiculares');
    }
}
