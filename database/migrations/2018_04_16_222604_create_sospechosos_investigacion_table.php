<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSospechososInvestigacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sospechosos_investigacion_ss', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_informe')->nullable();
            $table->integer('id_solicitud')->nullable();
            $table->string('alias')->nullable();
            $table->string('otros_nombres')->nullable();
            $table->text('caracteristicas')->nullable();
            $table->text('forma_cara')->nullable();
            $table->text('contextura')->nullable();
            $table->string('tono_voz')->nullable();
            $table->text('discapacidad')->nullable();
            $table->string('peso')->nullable();
            $table->string('estatura')->nullable();
            $table->string('tipo_sangre')->nullable();
            $table->text('cicatrices')->nullable();
            $table->string('zona')->nullable();
            $table->text('descripcion_zona')->nullable();
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
        Schema::dropIfExists('sospechosos_investigacion_ss');
    }
}
