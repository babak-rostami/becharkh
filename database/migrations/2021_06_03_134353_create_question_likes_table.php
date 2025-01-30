<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('question_likes')) {
            Schema::create('question_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('question_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip')->nullable();
                $table->boolean('like_or_unlike'); //true is like false is unlike
                $table->timestamps();

                $table->foreign('question_id')->references('id')->on('questions')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('question_likes');
    }
}
