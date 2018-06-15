<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_value', function (Blueprint $table) {
            $table->integer('report_id')->unsigned();
            $table->integer('form_field_id')->unsigned();
            $table->mediumText('value')->nullable();

            $table->primary(array('report_id', 'form_field_id'));

            $table->foreign('report_id')->references('id')->on('report');
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
        Schema::dropIfExists('report_value');
    }
}
