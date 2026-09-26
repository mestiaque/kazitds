<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class DataController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:admin')->only(['clearData']);
    }

    public function clearDataForm()
    {
        return view('kazitds::data.clear');
    }

    public function clearData(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|in:CLEAR ALL DATA',
        ]);

        try {
            // First clear data in transaction
            DB::transaction(function () {
                // Clear data in order to respect foreign key constraints
                DB::table('sale_returns')->delete();
                DB::table('purchase_returns')->delete();
                DB::table('sale_items')->delete();
                DB::table('sales')->delete();
                DB::table('purchases')->delete();
                DB::table('product_variants')->delete();
                DB::table('products')->delete();
                DB::table('brands')->delete();
                DB::table('packs')->delete();
                DB::table('suppliers')->delete();
                DB::table('customers')->delete();
                DB::table('payment_histories')->delete();
                DB::table('dues')->delete();
                DB::table('sms_accounts')->delete();
                DB::table('sms_logs')->delete();
            });

            // Reset auto increment outside of transaction
            DB::statement('ALTER TABLE sale_returns AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE purchase_returns AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE sale_items AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE sales AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE purchases AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE product_variants AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE brands AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE packs AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE suppliers AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE customers AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE payment_history AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE dues AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE sms_accounts AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE sms_logs AUTO_INCREMENT = 1');

            return redirect()->back()->with('success', 'All data cleared successfully. Users and roles are preserved.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error clearing data: ' . $e->getMessage());
        }
    }

    public function changeLocale($locale = 'en')
    {
        session(['locale' => $locale]);
        app()->setLocale($locale);
        return redirect()->back();
    }
}
