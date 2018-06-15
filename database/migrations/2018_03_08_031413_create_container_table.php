<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContainerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('form_id')->nullable()->unsigned();
            $table->string('name', 255);
            $table->boolean("reportable")->default(true);
            $table->integer('default_cover_image_id')->nullable()->unsigned();
            $table->boolean('title_duplicatable');
            $table->text('uid_rule')->nullable();
            $table->text('card_rule')->nullable();

            $table->foreign('parent_id')->references('id')->on('container');
            $table->foreign('form_id')->references('id')->on('dynamic_form');
            $table->foreign('default_cover_image_id')->references('id')->on('attachment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container');
    }
}
