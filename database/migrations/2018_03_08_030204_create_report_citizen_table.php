<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportCitizenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_citizen', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('comment', 255)->nullable();
            $table->string('source', 16)->nullable();
            $table->string('version', 16)->nullable();
            $table->double('lat', 15, 12)->nullable();
            $table->double('lng', 15, 12)->nullable();
            $table->boolean('is_read')->default(false);
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_citizen');
    }
}
