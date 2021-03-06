<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHitosInformesSsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hitos_informes_ss', function (Blueprint $table) {
          $table->increments('id');
          $table->string('nombre')->nullable();
          $table->longText('descripcion')->nullable();
          $table->date('fecha_inicio')->nullable();
          $table->date('fecha_fin')->nullable();
          $table->integer('id_informe')->nullable();
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
        Schema::dropIfExists('hitos_informes_ss');
    }
}
