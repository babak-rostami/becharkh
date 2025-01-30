<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('category_features')) {
            Schema::create('category_features', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id');

                $table->string('title');
                $table->string('title_en')->nullable();
                $table->string('slug')->nullable();
                $table->text('similar_search')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('image')->nullable();
                $table->boolean('status')->default(0);
                $table->boolean('is_in_filter_rtable')->default(0);
                $table->boolean('is_important_in_blog')->default(0);
                $table->tinyInteger('input_type')->default(0);

                $table->timestamps();
                $table->foreign('category_id')->references('id')->on('site_categories')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('parent_id')->references('id')->on('category_features')
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
        Schema::dropIfExists('category_features');
    }
}
