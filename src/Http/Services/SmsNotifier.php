<?php

namespace ME\Kazitds\Http\Services;

use ME\Services\SmsService;

/**
 * Customer SMS for sales and dues. Sending, logging and balance are handled by
 * metheme's SmsService; this class only builds the messages.
 */
class SmsNotifier
{
    public static function newSaleCreate($customer, $totalAmount, $paidAmount, $dueAmount): bool
    {
        return self::send($customer, self::billMessage($customer, $totalAmount, $paidAmount, $dueAmount));
    }

    public static function payment($customer, $totalAmount, $paidAmount, $dueAmount): bool
    {
        return self::send($customer, self::billMessage($customer, $totalAmount, $paidAmount, $dueAmount));
    }

    public static function notify($customer, $dueAmount): bool
    {
        $due = toBanglaNumber($dueAmount);
        $shopName = get_setting('shop_name');

        return self::send($customer, "প্রিয় {$customer->name}, \nআপনার বকেয়া {$due} টাকা পরিশোধের বিনীত অনুরোধ করছি।\n— {$shopName}");
    }

    private static function billMessage($customer, $totalAmount, $paidAmount, $dueAmount): string
    {
        $total = toBanglaNumber($totalAmount);
        $paid = toBanglaNumber($paidAmount);
        $due = toBanglaNumber($dueAmount);
        $shopName = get_setting('shop_name');

        return "প্রিয় {$customer->name}, \nমোট বিল: {$total} টাকা \nজমা: {$paid} টাকা \nবকেয়া: {$due} টাকা \n— {$shopName}";
    }

    private static function send($customer, string $message): bool
    {
        if (!get_setting('enable_sms') || empty($customer->phone)) {
            return false;
        }

        return SmsService::send($customer->phone, $message)['success'];
    }
}
