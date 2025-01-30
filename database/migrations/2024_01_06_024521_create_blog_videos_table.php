<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('blog_videos')) {
            Schema::create('blog_videos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('blog_id');
                $table->string('title');
                $table->string('path');
                $table->timestamps();

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
        Schema::dropIfExists('blog_videos');
    }
}
