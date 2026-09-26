<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:setting.edit')->only(['edit', 'update']);
        $this->middleware('authorization:setting.configurations')->only(['editConfigurations', 'updateConfigurations']);
    }

    public function edit()
    {
        // Get all settings
        $settings = [
            'shop_name' => Setting::get('shop_name', 'My Shop'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_email' => Setting::get('shop_email', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'shop_logo' => Setting::get('shop_logo'),
            'low_stock_threshold' => Setting::get('low_stock_threshold', 5),
            'sms_permit' => Setting::get('sms_permit'),
        ];

        return view('kazitds::settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'nullable|string',
            'shop_email' => 'nullable|email|max:255',
            'shop_phone' => 'nullable|string|max:50',
            'shop_logo' => 'nullable|image|max:2048',
            'low_stock_threshold' => 'required|integer|min:1',
            'sms_notifications' => 'nullable|array',
            'sms_notifications.*' => 'boolean',
        ]);

        // Update text settings
        Setting::set('shop_name', $request->shop_name);
        Setting::set('shop_address', $request->shop_address);
        Setting::set('shop_email', $request->shop_email);
        Setting::set('shop_phone', $request->shop_phone);
        Setting::set('low_stock_threshold', $request->low_stock_threshold);
        Setting::set('sms_notifications', $request->sms_notifications);

        if ($request->hasFile('shop_logo')) {
            $image = $request->file('shop_logo');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = storage_path('app/public/images/shop_logo');

            // Ensure the directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);
            // $data['shop_logo'] = $imageName;
            Setting::set('shop_logo', $imageName);
        }

        return redirect()->route('settings.edit')
            ->with('success', __('kazitds::kazitds.Settings updated successfully'));
    }

    public function editConfigurations()
    {
        // Get all settings
        $settings = [
            'show_discount_option' => (bool) Setting::get('show_discount_option', true),
            'show_previous_due_option' => (bool) Setting::get('show_previous_due_option', true),
            'pagination' => (int) Setting::get('pagination', 10),
            'enable_translation' => (bool) Setting::get('enable_translation', false),
            'enable_sms' => (bool) Setting::get('enable_sms', false),
        ];

        return view('kazitds::settings.configurations', compact('settings'));
    }

    public function updateConfigurations(Request $request)
    {
        // Store boolean values properly
        Setting::set('show_discount_option', $request->has('show_discount_option'));
        Setting::set('show_previous_due_option', $request->has('show_previous_due_option'));
        Setting::set('pagination', (int) $request->pagination);
        Setting::set('enable_translation', $request->has('enable_translation'));
        Setting::set('enable_sms', $request->has('enable_sms'));

        return redirect()->route('configurations.edit')
            ->with('success', __('kazitds::kazitds.Configurations updated successfully'));
    }
}
