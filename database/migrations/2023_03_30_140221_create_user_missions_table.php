<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_missions')) {
            Schema::create('user_missions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mission_id');
                $table->unsignedBigInteger('user_id');
                $table->integer('done_count')->nullable();
                $table->boolean('get_reward')->default(0);
                $table->timestamps();
                $table->foreign('mission_id')->references('id')->on('missions')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('user_missions');
    }
}
