<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->increments('id');
            $table->DateTime('fecha')->nullable();
            $table->string('titulo')->nullable();
            $table->string('numero_oficio')->nullable();
            $table->string('institucion')->nullable();
            $table->string('solicitado_por')->nullable();
            $table->string('solicitable_type')->nullable();
            $table->integer('solicitable_id')->nullable();
            $table->longText('descripcion')->nullable();
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
        Schema::dropIfExists('solicitudes');
    }
}
