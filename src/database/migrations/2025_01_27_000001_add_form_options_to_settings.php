<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ME\Kazitds\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add default settings for form options
        Setting::set('show_discount_option', true);
        Setting::set('show_previous_due_option', true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::forget('show_discount_option');
        Setting::forget('show_previous_due_option');
    }
};
