<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('site_categories')) {
            Schema::create('site_categories', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_en')->nullable();
                $table->string('slug')->nullable();
                $table->text('similar_search')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('image')->nullable();

                $table->boolean('status')->default(0);
                $table->timestamps();
                $table->foreign('parent_id')->references('id')->on('site_categories')
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
        Schema::dropIfExists('site_categories');
    }
}
