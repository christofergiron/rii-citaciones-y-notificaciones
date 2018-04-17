<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('armas_ss', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_tipo_arma')->nullable();
            $table->integer('id_sospechoso_investigacion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('calibre')->nullable();
            $table->string('modelo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('serial')->nullable();
            $table->string('marca')->nullable();
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
        Schema::dropIfExists('armas_ss');
    }
}
