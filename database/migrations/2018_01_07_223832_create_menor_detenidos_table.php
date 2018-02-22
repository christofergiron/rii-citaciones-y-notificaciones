<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenorDetenidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menores_detenidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fiscal_niñez')->nullable();
            $table->integer('apoderado')->nullable();
            $table->DateTime('fecha_remision_centro_especializado')->nullable();
            $table->integer('centro_especializado')->nullable();
            $table->string('workflow_state')->nullable();
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
        Schema::dropIfExists('menores_detenidos');
    }
}
