<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('race_comments')) {
            Schema::create('race_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('reply_to_id')->nullable();
                $table->unsignedBigInteger('race_id');
                $table->string('name');
                $table->string('email');
                $table->text('body');
                $table->timestamps();
                $table->foreign('parent_id')->references('id')->on('blog_comments')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('race_id')->references('id')->on('races')
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
        Schema::dropIfExists('race_comments');
    }
}
