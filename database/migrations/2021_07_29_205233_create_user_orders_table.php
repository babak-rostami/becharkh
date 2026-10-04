<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_orders')) {
            Schema::create('user_orders', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_id');
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('transaction_number');
                $table->string('payment_reference_id')->nullable();
                $table->string('amount');
                $table->text('address')->nullable();
                $table->string('status')->default(0);
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
        Schema::dropIfExists('user_orders');
    }
}
