<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoryFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('category_features', 'is_important_in_ad') && !Schema::hasColumn('category_features', 'is_in_filter_ad') && !Schema::hasColumn('category_features', 'is_in_page_title')) {
            Schema::table('category_features', function (Blueprint $table) {
                $table->renameColumn('is_important_in_blog', 'is_important_in_ad');
                $table->boolean('is_in_filter_ad')->default(0);
                $table->boolean('is_in_page_title')->default(0);
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
            $table->renameColumn('is_important_in_ad', 'is_important_in_blog');
            $table->dropColumn('is_in_filter_ad');
            $table->dropColumn('is_in_page_title');
        });
    }
}
