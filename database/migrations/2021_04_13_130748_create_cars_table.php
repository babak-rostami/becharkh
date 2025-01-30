<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cars')) {
            Schema::create('cars', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('brand_id');
                $table->unsignedBigInteger('model_id');

                $table->string('production_year');
                $table->string('how_sell');
                $table->string('kilometer');
                $table->string('gearbox');
                $table->string('fuel_type');
                $table->string('body_condition');
                $table->integer('priority')->nullable();

                $table->timestamps();

                $table->foreign('brand_id')->references('id')->on('brands')
                    ->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('model_id')->references('id')->on('car_models')
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
        Schema::dropIfExists('cars');
    }
}
