<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReporterLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporter_location', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device_id', 32);
            $table->double('lat', 15, 12)->nullable();
            $table->double('lng', 15, 12)->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->timestamps();
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
        Schema::dropIfExists('reporter_location');
    }
}
