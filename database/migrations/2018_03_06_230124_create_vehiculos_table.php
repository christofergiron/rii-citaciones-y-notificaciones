<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo');
            $table->string('marca');
            $table->string('modelo');
            $table->string('placa')->nullable();
            $table->integer('año')->nullable();
            $table->string('color')->nullable();
            $table->string('estado')->nullable();
            $table->string('motor')->nullable();
            $table->string('chasis')->nullable();
            $table->string('vin')->nullable();
            $table->string('descripcion')->nullable();
            $table->integer('id_propietario')->nullable();
            $table->string('licencia')->nullable();
            $table->integer('id_unidad')->nullable();
            $table->integer('id_funcionario');
            $table->DateTime('fecha_registro');
            $table->integer('id_denuncia')->nullable();
            $table->integer('id_orden_captura')->nullable();
            $table->integer('id_lugar')->nullable();

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
        Schema::dropIfExists('vehiculos');
    }
}
