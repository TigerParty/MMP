<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('region', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_id')->nullable()->unsigned();
            $table->string('label_name', 32)->nullable();
            $table->integer('order')->default(0)->unsigned();
            $table->softDeletes();
            $table->string("capital", 255)->nullable()->default(null);
            $table->float("capital_lat", 15, 12)->nullable()->default(null);
            $table->float("capital_lng", 15, 12)->nullable()->default(null);
            $table->text('map_path')->nullable();
            $table->string('map_title_x',16)->nullable();
            $table->string('map_title_y',16)->nullable();

            $table->foreign('label_name')->references('name')->on('region_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('region');
    }
}
