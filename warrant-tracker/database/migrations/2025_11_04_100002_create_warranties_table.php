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
            
            // Foreign key to the user who owns this warranty
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Foreign key to the product this warranty is for
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->string('serial_number')->nullable()->unique();
            $table->date('purchase_date');
            $table->integer('duration_months'); // e.g., 12, 24, 36
            $table->date('expiry_date')->as('DATE(purchase_date + INTERVAL \'1 MONTH\' * duration_months)'); // Calculated expiry date for Postgres
            $table->string('provider'); // e.g., "Manufacturer", "Best Buy"
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
