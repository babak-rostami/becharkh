<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertiseFeatureValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('advertise_feature_values')) {
            Schema::create('advertise_feature_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('advertise_id');
                $table->unsignedBigInteger('feature_id');
                $table->string('value')->nullable();
                $table->timestamps();
                $table->foreign('advertise_id')->references('id')->on('advertises')
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
        Schema::dropIfExists('advertise_feature_values');
    }
}
