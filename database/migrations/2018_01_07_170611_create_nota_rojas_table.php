<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotaRojasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notas_rojas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('workflow_state')->nullable();
            $table->integer('identidad_persona');
            $table->string('nombre_persona');
            $table->string('nacionalidad');
            $table->string('descripcion');
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
        Schema::dropIfExists('notas_rojas');
    }
}
