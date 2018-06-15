<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndicatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicator', function (Blueprint $table) {
            $table->increments('id');
            $table->json('title');
            $table->integer('indicate_id', false, 11);
            $table->string('indicate_type', 255);
            $table->string('rule');
            $table->json('options');
            $table->json('yaxis');
            $table->integer('xaxis_limit', false, 3);
            $table->json('data_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indicator');
    }
}
