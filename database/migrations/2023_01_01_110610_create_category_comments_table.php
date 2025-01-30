<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('category_comments')) {
            Schema::create('category_comments', function (Blueprint $table) {
                $table->id();


                $table->unsignedBigInteger('category_id')->nullable();
                //ریپلای زیر کدام کامنت است
                $table->unsignedBigInteger('parent_id')->nullable();
                //به کدام کامنت ریپلای زده است
                $table->unsignedBigInteger('reply_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->text('body');
                $table->boolean('status')->default(1);

                $table->timestamps();
                $table->foreign('category_id')->references('id')->on('site_categories')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('parent_id')->references('id')->on('category_comments')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('reply_id')->references('id')->on('category_comments')
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
        Schema::dropIfExists('category_comments');
    }
}
