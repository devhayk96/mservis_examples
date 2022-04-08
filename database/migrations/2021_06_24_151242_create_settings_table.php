<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');

            $table
                ->dateTime('queue_restarted_at')
                ->nullable()
                ->comment('Queue worker start/restart time.');

            $table
                ->dateTime('queue_high_restarted_at')
                ->nullable()
                ->comment('Hight priority queue worker start/restart time.');
        });

        DB::statement("ALTER TABLE `settings` comment 'General system settings.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}