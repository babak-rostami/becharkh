<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('races')) {
            Schema::create('races', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('creator_id');
                $table->string('creator_class');

                $table->string('image');
                $table->string('title');
                $table->string('slug');
                $table->text('body')->nullable();
                $table->dateTime('end_time');

                $table->boolean('status')->default(0);

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
        Schema::dropIfExists('races');
    }
}
