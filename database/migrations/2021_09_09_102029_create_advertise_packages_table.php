<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('advertise_packages')) {
            Schema::create('advertise_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('advertise_count');
                $table->integer('ad_to_top_count');
                $table->integer('image_count');
                $table->string('price');
                $table->integer('date_number');
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
        Schema::dropIfExists('advertise_packages');
    }
}
