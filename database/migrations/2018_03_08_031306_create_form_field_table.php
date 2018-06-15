<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_field', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->integer('form_id')->nullable()->unsigned();
            $table->integer('field_template_id')->nullable()->unsigned();
            $table->json('options')->nullable();
            $table->mediumText('default_value')->nullable();
            $table->integer('view_level_id')->nullable()->unsigned();
            $table->integer('edit_level_id')->nullable()->unsigned();
            $table->integer('order')->nullable()->unsigned();
            $table->softDeletes();
            $table->json('show_if')->nullable();
            $table->boolean('is_required')->default(false);
            $table->text('formula')->nullable();

            $table->foreign('form_id')->references('id')->on('dynamic_form');
            $table->foreign('field_template_id')->references('id')->on('field_template');
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
        Schema::dropIfExists('form_field');
    }
}
