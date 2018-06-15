<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationProjectHasForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_project_has_form', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('form_id')->unsigned();

            $table->primary(array('project_id', 'form_id'));

            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('form_id')->references('id')->on('dynamic_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_project_has_form');
    }
}
