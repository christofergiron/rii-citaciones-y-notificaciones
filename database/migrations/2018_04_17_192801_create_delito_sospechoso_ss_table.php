<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelitoSospechosoSsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delito_sospechoso_ss', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sospechoso_id')->nullable();
            $table->integer('delito_id')->nullable();
            $table->tinyInteger('culposo')->nullable();
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
        Schema::dropIfExists('delito_sospechoso_ss');
    }
}
