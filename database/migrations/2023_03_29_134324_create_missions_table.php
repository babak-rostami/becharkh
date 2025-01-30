<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('missions')) {
            Schema::create('missions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('feature_id')->nullable();
                $table->unsignedBigInteger('item_id')->nullable();
                $table->integer('type');
                $table->integer('done_count')->default(1);
                $table->integer('priority');
                $table->date('expire_time')->nullable();
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('site_categories')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('feature_id')->references('id')->on('category_features')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('missions');
    }
}
