<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_value', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('form_field_id')->unsigned();
            $table->mediumText('value')->nullable();

            $table->primary(array('project_id', 'form_field_id'));

            $table->foreign('project_id')->references('id')->on('project');
            $table->foreign('form_field_id')->references('id')->on('form_field');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_value');
    }
}
