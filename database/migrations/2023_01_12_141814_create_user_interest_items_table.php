<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInterestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_interest_items')) {
            Schema::create('user_interest_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('feature_id');
                $table->unsignedBigInteger('item_id');
                $table->bigInteger('seen_count')->default(0);
                $table->bigInteger('ad_page_seen_count')->default(0);
                $table->bigInteger('rtable_page_seen_count')->default(0);
                $table->bigInteger('ccomment_page_seen_count')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('category_id')->references('id')->on('site_categories')
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
        Schema::dropIfExists('user_interest_items');
    }
}
