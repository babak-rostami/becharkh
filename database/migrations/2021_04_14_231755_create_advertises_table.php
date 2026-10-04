<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ad_id');
            $table->string('ad_class');
            $table->unsignedBigInteger('user_id');

            $table->string('random_id');
            $table->string('title');
            $table->string('slug');
            $table->bigInteger('seen_count')->default(0);

            $table->text('body');
            $table->integer('ostan');
            $table->integer('city');
            $table->string('price')->nullable();
            $table->string('phone')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('advertises');
    }
}
