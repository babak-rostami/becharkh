<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarModelCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('car_model_comment_likes')) {
            Schema::create('car_model_comment_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('model_comment_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip')->nullable();
                $table->boolean('like_or_unlike'); //true is like false is unlike
                $table->timestamps();

                $table->foreign('model_comment_id')->references('id')->on('car_model_comments')
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
        Schema::dropIfExists('car_model_comment_likes');
    }
}
