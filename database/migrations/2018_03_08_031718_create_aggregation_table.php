<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAggregationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 32);
            $table->string('type', 255);
            $table->integer('container_id')->nullable()->unsigned();
            $table->integer('target_container_id')->unsigned();
            $table->integer('target_field_id')->nullable()->unsigned();
            $table->string('filters', 255)->nullable();
            $table->integer('order')->nullable()->unsigned();

            $table->foreign('container_id')->references('id')->on('container');
            $table->foreign('target_container_id')->references('id')->on('container');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregation');
    }
}
