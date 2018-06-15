<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationProjectHasChart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_project_has_chart', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('chart_id')->unsigned();

            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('chart_id')->references('id')->on('chart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_project_has_chart');
    }
}
