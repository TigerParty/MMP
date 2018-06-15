<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receiver', 255)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('schedule', 20)->nullable();
            $table->morphs('notify');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_sms');
    }
}
