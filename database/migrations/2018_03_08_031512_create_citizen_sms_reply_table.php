<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitizenSmsReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citizen_sms_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('citizen_sms_id')->unsigned();
            $table->string('message', 160);
            $table->timestamps();

            $table->foreign('citizen_sms_id')->references('id')->on('citizen_sms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citizen_sms_reply');
    }
}
