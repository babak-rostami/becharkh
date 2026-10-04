<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowToCategoryFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('category_features', 'has_follow')) {
            Schema::table('category_features', function (Blueprint $table) {
                $table->boolean('has_follow')->default(0);
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
        Schema::table('category_features', function (Blueprint $table) {
            $table->dropColumn('has_follow');
        });
    }
}
