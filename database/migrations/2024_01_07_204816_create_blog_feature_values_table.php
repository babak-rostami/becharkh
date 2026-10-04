<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogFeatureValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('blog_feature_values')) {
            Schema::create('blog_feature_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('blog_id');
                $table->unsignedBigInteger('feature_id');
                $table->unsignedBigInteger('item_id');
                $table->timestamps();
                $table->foreign('blog_id')->references('id')->on('blogs')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('feature_id')->references('id')->on('category_features')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('item_id')->references('id')->on('category_feature_items')
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
        Schema::dropIfExists('blog_feature_values');
    }
}
