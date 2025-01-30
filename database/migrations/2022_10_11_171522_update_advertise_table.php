<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdvertiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('advertises', 'category_id')) {
            Schema::table('advertises', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable();
                // $table->index(['random_id', 'slug']);

                $table->foreign('category_id')->references('id')->on('site_categories')
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
        Schema::table('advertises', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
}
