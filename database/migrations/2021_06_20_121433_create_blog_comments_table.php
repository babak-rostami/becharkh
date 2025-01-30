<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('blog_comments')) {
            Schema::create('blog_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('reply_to_id')->nullable();
                $table->unsignedBigInteger('blog_id');
                $table->string('name');
                $table->string('email');
                $table->text('body');
                $table->timestamps();
                $table->foreign('parent_id')->references('id')->on('blog_comments')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('blog_id')->references('id')->on('blogs')
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
        Schema::dropIfExists('blog_comments');
    }
}
