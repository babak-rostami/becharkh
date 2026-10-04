<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_messages')) {
            Schema::create('user_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('from_user')->nullable();
                $table->unsignedBigInteger('to_user')->nullable();
                $table->unsignedBigInteger('parent')->nullable();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->text('body');
                $table->boolean('seen')->default(0);
                $table->timestamps();

                $table->foreign('from_user')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('to_user')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('parent')->references('id')->on('user_messages')
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
        Schema::dropIfExists('user_messages');
    }
}
