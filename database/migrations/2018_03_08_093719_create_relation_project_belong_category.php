<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationProjectBelongCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_project_belong_category', function(Blueprint $table)
        {
            $table->integer('project_id', false, 11);
            $table->integer('category_id', false, 11);

            $table->primary(array('project_id', 'category_id'));

            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('category_id')->references('id')->on('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_project_belong_category');
    }
}
