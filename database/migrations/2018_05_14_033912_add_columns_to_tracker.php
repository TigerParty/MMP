<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// use DB;

class AddColumnsToTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE tracker MODIFY COLUMN meta JSON AFTER path");

        Schema::table('tracker', function (Blueprint $table) {
            $table->mediumText('description')->nullable()->after('meta');
            $table->string('source')->nullable()->after('title');
            $table->double('lat', 15, 8)->nullable()->after('source');
            $table->double('lng', 15, 8)->nullable()->after('lat');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracker', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('source');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });

        DB::statement("ALTER TABLE tracker MODIFY COLUMN meta JSON AFTER deleted_at");
    }
}
