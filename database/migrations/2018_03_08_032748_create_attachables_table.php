<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attachment_id')->nullable()->unsigned();

            $table->morphs('attachable');

            $table->integer('attached_form_id')->nullable()->unsigned();
            $table->datetime('attached_at')->nullable();
            $table->json('description')->nullable();

            $table->foreign('attachment_id')->references('id')->on('attachment');
            $table->foreign('attached_form_id')->references('id')->on('dynamic_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachables');
    }
}
