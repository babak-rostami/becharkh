<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('race_options')) {
            Schema::create('race_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('race_id');

                $table->string('title');
                $table->timestamps();

                $table->foreign('race_id')->references('id')->on('races')
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
        Schema::dropIfExists('race_options');
    }
}
