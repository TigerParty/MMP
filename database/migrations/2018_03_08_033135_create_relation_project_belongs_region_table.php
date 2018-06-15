<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationProjectBelongsRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_project_belongs_region', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('region_id')->unsigned();

            $table->primary(array('project_id', 'region_id'));

            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('region_id')->references('id')->on('region');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_project_belongs_region');
    }
}
