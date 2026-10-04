<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('advertise_id')->nullable();
            //ریپلای زیر کدام کامنت است
            $table->unsignedBigInteger('parent_id')->nullable();
            //به کدام کامنت ریپلای زده است
            $table->unsignedBigInteger('reply_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('body');

            $table->timestamps();
            $table->foreign('advertise_id')->references('id')->on('advertises')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('parent_id')->references('id')->on('comments')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reply_id')->references('id')->on('comments')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
