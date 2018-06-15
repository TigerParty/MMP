<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationFieldFilterContainerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relation_field_filter_container', function (Blueprint $table) {
            $table->integer('form_field_id')->unsigned();
            $table->integer('container_id')->unsigned();

            $table->primary(array('form_field_id', 'container_id'), 'relation_field_container_primary');

            $table->foreign('form_field_id')->references('id')->on('form_field');
            $table->foreign('container_id')->references('id')->on('container');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relation_field_filter_container');
    }
}
