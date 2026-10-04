<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryFeatureItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('category_feature_items')) {
            Schema::create('category_feature_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('feature_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('image')->nullable();
                $table->string('title')->nullable();
                $table->string('title_en')->nullable();
                $table->string('slug')->nullable();
                $table->text('similar_search')->nullable();
                $table->boolean('status')->default(0);
                $table->timestamps();

                $table->foreign('feature_id')->references('id')->on('category_features')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('parent_id')->references('id')->on('category_feature_items')
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
        Schema::dropIfExists('category_feature_items');
    }
}
