<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->nullable()->unsigned();
            $table->integer('project_id')->nullable()->unsigned();
            $table->mediumText('description')->nullable();
            $table->double('lat', 15, 12)->nullable();
            $table->double('lng', 15, 12)->nullable();
            $table->integer('view_level_id')->nullable()->unsigned();
            $table->integer('edit_level_id')->nullable()->unsigned();
            $table->integer('created_by')->nullable()->unsigned();
            $table->string('version', 255)->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->json('region_ids')->nullable();

            $table->foreign('form_id')->references('id')->on('dynamic_form');
            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('created_by')->references('id')->on('user');
            $table->foreign('updated_by')->references('id')->on('user');
            $table->foreign('view_level_id')->references('id')->on('permission_level');
            $table->foreign('edit_level_id')->references('id')->on('permission_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report');
    }
}
