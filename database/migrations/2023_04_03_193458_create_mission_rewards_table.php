<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mission_rewards')) {
            Schema::create('mission_rewards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mission_id');
                $table->string('title');
                $table->integer('amount');
                $table->timestamps();
                $table->foreign('mission_id')->references('id')->on('missions')
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
        Schema::dropIfExists('mission_rewards');
    }
}
