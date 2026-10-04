<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelDatailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('model_datails')) {
            Schema::create('model_datails', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('seen_count')->default(0);
                $table->unsignedBigInteger('model_id');
                $table->unsignedBigInteger('brand_id');
                $table->string('production_year'); //سال ساخت
                $table->string('cylinder'); //تعداد سیلندر

                $table->string('image');

                $table->text('description')->nullable();
                $table->string('engine_volume')->nullable(); //حجم موتور
                $table->string('acceleration')->nullable(); //شتاب
                $table->string('engine_power')->nullable(); //قدرت موتور
                $table->string('torque')->nullable(); //گشتاور
                $table->string('max_speed')->nullable(); //حداکثر سرعت
                $table->string('gearbox')->nullable(); //گیربکس
                $table->string('differential')->nullable(); //دیفرانسیل
                $table->string('body_class')->nullable(); //کلاس بدنه
                $table->string('car_weight')->nullable(); //وزن خودرو
                $table->string('fuel_consumption')->nullable(); //مصرف سوخت
                $table->string('buck_volume')->nullable(); //حجم باک
                $table->string('country')->nullable(); //کشور سازنده
                $table->string('brake_system')->nullable(); //سیستم ترمز
                $table->string('steering_system')->nullable(); //سیستم فرمان
                $table->string('media_system')->nullable(); //سیستم مدیا
                $table->string('mirror_glass_system')->nullable(); //سیستم آینه و شیشه
                $table->string('lighting')->nullable(); //روشنایی
                $table->string('welfare')->nullable(); //رفاهی

                $table->string('power_points')->nullable(); //نقاط قوت
                $table->string('weak_points')->nullable(); //نقاط ضعف

                $table->text('others')->nullable();

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
        Schema::dropIfExists('model_datails');
    }
}
