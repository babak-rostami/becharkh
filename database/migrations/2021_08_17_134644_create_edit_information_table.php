<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEditInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('edit_information')) {
            Schema::create('edit_information', function (Blueprint $table) {
                $table->id();
                $table->text('url')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email')->nullable();
                $table->text('body');
                $table->boolean('status')->default(0);
                $table->timestamps();

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
        Schema::dropIfExists('edit_information');
    }
}
