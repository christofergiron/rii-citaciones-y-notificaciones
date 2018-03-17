<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitudAnalisisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_analisis', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('id_laboratorio');
          $table->string('nombre_laboratorio');
          $table->integer('tipo_analisis');
          $table->string('nombre_analisis');
          $table->string('detalle_analisis');
          $table->string('workflow_state')->nullable();
          //se necesita la tabla evidencias x analisis
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
        Schema::dropIfExists('solicitudes_analisis');
    }
}
