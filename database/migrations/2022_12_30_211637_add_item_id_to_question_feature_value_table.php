<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemIdToQuestionFeatureValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('question_feature_values', 'item_id')) {
            Schema::table('question_feature_values', function (Blueprint $table) {
                $table->unsignedBigInteger('item_id')->nullable();
                $table->foreign('item_id')->references('id')->on('category_feature_items')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('question_feature_values', function (Blueprint $table) {
            $table->dropColumn('item_id');
        });
    }
}
