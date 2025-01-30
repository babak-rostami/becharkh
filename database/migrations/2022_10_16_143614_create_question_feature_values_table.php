<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionFeatureValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('question_feature_values')) {
            Schema::create('question_feature_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('question_id');
                $table->unsignedBigInteger('feature_id');
                $table->string('value')->nullable();
                $table->timestamps();
                $table->foreign('question_id')->references('id')->on('questions')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('feature_id')->references('id')->on('category_features')
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
        Schema::dropIfExists('question_feature_values');
    }
}
