<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowFeatureItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('follow_feature_items')) {
            Schema::create('follow_feature_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('feature_id');
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('user_id');

                $table->foreign('feature_id')->references('id')->on('category_features')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('item_id')->references('id')->on('category_feature_items')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('follow_feature_items');
    }
}
