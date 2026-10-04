<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeenCountToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('category_feature_items', 'ad_seen_count') && !Schema::hasColumn('category_feature_items', 'rtable_seen_count') && !Schema::hasColumn('category_feature_items', 'ccomment_seen_count')) {
            Schema::table('category_feature_items', function (Blueprint $table) {
                $table->bigInteger('ad_seen_count')->default('0');
                $table->bigInteger('rtable_seen_count')->default('0');
                $table->bigInteger('ccomment_seen_count')->default('0');
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
        Schema::table('category_feature_items', function (Blueprint $table) {
            $table->dropColumn('ad_seen_count');
            $table->dropColumn('rtable_seen_count');
            $table->dropColumn('ccomment_seen_count');
        });
    }
}
