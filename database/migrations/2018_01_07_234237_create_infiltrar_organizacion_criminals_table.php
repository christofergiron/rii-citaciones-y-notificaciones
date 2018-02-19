<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfiltrarOrganizacionCriminalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infiltrar_organizaciones_criminales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_orden');
            $table->string('workflow_state')->nullable();
            $table->integer('id_agente')->nullable();
            $table->string('nombre_organizacion')->nullable();
            $table->DateTime('fecha_infiltracion')->nullable();
            $table->DateTime('fecha_finalizacion')->nullable();
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
        Schema::dropIfExists('infiltrar_organizaciones_criminales');
    }
}
