<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryCommentFeatureValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('category_comment_feature_values')) {
            Schema::create('category_comment_feature_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('comment_id');
                $table->unsignedBigInteger('feature_id');
                $table->unsignedBigInteger('item_id')->nullable();

                $table->string('value')->nullable();
                $table->timestamps();
                $table->foreign('comment_id')->references('id')->on('category_comments')
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
        Schema::dropIfExists('category_comment_feature_values');
    }
}
