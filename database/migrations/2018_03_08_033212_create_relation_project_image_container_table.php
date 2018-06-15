<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationProjectImageContainerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_project_image_container', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('container_id')->unsigned();
            $table->integer('cover_image_id')->nullable()->unsigned();

            $table->primary(array('project_id', 'container_id'));

            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('container_id')->references('id')->on('container');
            $table->foreign('cover_image_id')->references('id')->on('attachment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_project_image_container');
    }
}
