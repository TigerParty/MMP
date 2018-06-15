<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitizenSMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citizen_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->nullable()->unsigned();
            $table->string('message', 160)->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_approved')->default(false)->unsigned()->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citizen_sms');
    }
}
