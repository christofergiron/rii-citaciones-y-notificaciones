<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetenidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detenidos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipoable_type')->nullable();
            $table->integer('tipoable_id')->nullable();
            $table->integer('id_orden')->nullable();
            $table->integer('id_requerimiento')->nullable();
            $table->integer('id_expediente')->nullable();
            $table->integer('id_captura');
            $table->Date('fecha_nacimiento')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('genero');
            $table->string('sexo');
            $table->integer('edad');
            $table->integer('lugar_retencion');
            $table->integer('id_fiscal')->nullable();
            $table->integer('id_investigador')->nullable();
            $table->integer('id_abogado_defensor')->nullable();
            $table->DateTime('fecha_remision_mp')->nullable();
            $table->integer('id_fiscalia_remision')->nullable();
            $table->DateTime('fecha_remision_pj')->nullable();
            $table->integer('id_juzgado_remision')->nullable();
            $table->DateTime('fecha_remision_penal')->nullable();
            $table->integer('id_penal')->nullable();
            $table->DateTime('fecha_extradicion')->nullable();
            $table->string('pais_extraditado')->nullable();
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
        Schema::dropIfExists('detenidos');
    }
}
