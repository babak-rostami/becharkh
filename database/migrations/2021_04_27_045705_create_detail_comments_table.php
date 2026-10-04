<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('detail_comments')) {
            Schema::create('detail_comments', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('detail_id')->nullable();
                //ریپلای زیر کدام کامنت است
                $table->unsignedBigInteger('parent_id')->nullable();
                //به کدام کامنت ریپلای زده است
                $table->unsignedBigInteger('reply_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->text('body');
                $table->boolean('status')->default(1);

                $table->timestamps();
                $table->foreign('detail_id')->references('id')->on('model_datails')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('parent_id')->references('id')->on('detail_comments')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('reply_id')->references('id')->on('detail_comments')
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
        Schema::dropIfExists('detail_comments');
    }
}
