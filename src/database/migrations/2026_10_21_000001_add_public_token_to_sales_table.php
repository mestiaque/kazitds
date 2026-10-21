<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Random token for the public invoice link (QR code), so invoices can't be found by guessing the number
        Schema::table('sales', function (Blueprint $table) {
            $table->string('public_token', 40)->nullable()->unique()->after('invoice_number');
        });

        // lazyById, not each(): each() pages by offset and skips rows once updated rows drop out of whereNull
        DB::table('sales')->whereNull('public_token')->lazyById()->each(function ($sale) {
            DB::table('sales')->where('id', $sale->id)->update(['public_token' => Str::random(32)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
