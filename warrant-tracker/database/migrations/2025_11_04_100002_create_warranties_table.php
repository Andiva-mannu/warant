<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This table links a User to a Product and tracks the warranty
       Schema::create('warranties', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('product_id');
    $table->string('customer_name');
    $table->string('serial_number')->nullable();
    $table->date('purchase_date');
    $table->integer('duration_months');
    $table->enum('status', ['active', 'claimed', 'expired'])->default('active');
    $table->date('expiry_date');       // define only once, without 'after'
    $table->string('provider');
    $table->text('notes')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
