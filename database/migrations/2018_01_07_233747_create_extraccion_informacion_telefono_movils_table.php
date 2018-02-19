<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtraccionInformacionTelefonoMovilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extraccion_informacion_telefonos_moviles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_orden');
            $table->string('workflow_state')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('tipo');
            $table->string('marca');
            $table->string('modelo');
            $table->string('numero');
            $table->DateTime('fecha_aprobacion')->nullable();
            $table->DateTime('fecha_extraccion')->nullable();
            $table->integer('lugar_extraccion')->nullable();
            $table->integer('ejecutor');
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
        Schema::dropIfExists('extraccion_informacion_telefonos_moviles');
    }
}
