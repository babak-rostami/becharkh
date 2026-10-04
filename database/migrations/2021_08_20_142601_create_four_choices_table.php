<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFourChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('four_choices')) {
            Schema::create('four_choices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title');
                $table->string('true_answer');
                $table->string('wrong_answer1');
                $table->string('wrong_answer2');
                $table->string('wrong_answer3');

                $table->integer('true_click')->default(0);
                $table->integer('wrong_click1')->default(0);
                $table->integer('wrong_click2')->default(0);
                $table->integer('wrong_click3')->default(0);

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
        Schema::dropIfExists('four_choices');
    }
}
