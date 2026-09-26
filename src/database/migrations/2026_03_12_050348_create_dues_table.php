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
        Schema::create('dues', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id');

            $table->decimal('sale_amount',10,2)->default(0.00);
            $table->decimal('paid_amount',10,2)->default(0.00);
            $table->decimal('due',10,2)->default(0.00);
            $table->decimal('previous_due',10,2)->default(0.00);
            $table->decimal('total_due',10,2)->default(0.00);

            $table->timestamps();

            // Foreign Keys
            $table->foreign('sale_id')
                  ->references('id')
                  ->on('sales')
                  ->onDelete('cascade');

            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues');
    }
};
