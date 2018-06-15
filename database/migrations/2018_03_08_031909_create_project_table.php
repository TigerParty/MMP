<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->nullable()->unsigned();
            $table->string('title', 255)->nullable();
            $table->mediumText('description')->nullable();
            $table->integer('view_level_id')->nullable()->unsigned();
            $table->integer('edit_level_id')->nullable()->unsigned();
            $table->integer('default_form_id')->nullable()->unsigned();
            $table->integer('project_status_id')->nullable()->unsigned();
            $table->integer('default_img_id')->nullable()->unsigned();
            $table->integer('cover_image_id')->nullable()->unsigned();
            $table->double('lat', 15, 12)->nullable();
            $table->double('lng', 15, 12)->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('container_id')->nullable()->unsigned();
            $table->integer('uid')->nullable()->unsigned();

            $table->foreign('default_form_id')->references('id')->on('dynamic_form');
            $table->foreign('default_img_id')->references('id')->on('attachment');
            $table->foreign('view_level_id')->references('id')->on('permission_level');
            $table->foreign('edit_level_id')->references('id')->on('permission_level');
            $table->foreign('project_status_id')->references('id')->on('project_status');
            $table->foreign('parent_id')->references('id')->on('project');
            $table->foreign('container_id')->references('id')->on('container');
            $table->foreign('cover_image_id')->references('id')->on('attachment');
            $table->foreign('created_by')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project');
    }
}
