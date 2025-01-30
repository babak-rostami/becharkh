<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionAnswerLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('question_answer_likes')) {
            Schema::create('question_answer_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('question_answer_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip')->nullable();
                $table->boolean('like_or_unlike'); //true is like false is unlike
                $table->timestamps();

                $table->foreign('question_answer_id')->references('id')->on('question_answers')
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
        Schema::dropIfExists('question_answer_likes');
    }
}
