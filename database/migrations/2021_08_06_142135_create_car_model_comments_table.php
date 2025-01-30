<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarModelCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('car_model_comments')) {
            Schema::create('car_model_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('model_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('reply_to_id')->nullable();

                $table->unsignedBigInteger('year_id')->nullable();
                $table->unsignedBigInteger('trim_id')->nullable();

                $table->tinyInteger('car_topic_category')->nullable();

                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('email');
                $table->text('body');
                $table->foreign('parent_id')->references('id')->on('car_model_comments')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('model_id')->references('id')->on('car_models')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->timestamps();
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
        Schema::dropIfExists('car_model_comments');
    }
}
