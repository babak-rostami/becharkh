<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceOptionPercentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('race_option_percents')) {
            Schema::create('race_option_percents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('option_id');
                $table->string('ip');

                $table->timestamps();

                $table->foreign('option_id')->references('id')->on('race_options')
                    ->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_option_percents');
    }
}
