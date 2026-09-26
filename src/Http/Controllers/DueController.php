<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Sale;
use ME\Kazitds\Models\Customer;
use ME\Kazitds\Http\Services\SmsNotifier;
use Illuminate\Http\Request;
use ME\Kazitds\Models\PaymentHistory;
use Illuminate\Support\Facades\DB;
use ME\Kazitds\Http\Services\CustomerSaleLedgerService;

class DueController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:due.view')->only(['index', 'show', 'storePayment']);
        $this->middleware('authorization:due.notify')->only(['notifyCustomer']);
    }

    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('customer_name')) {
            $query->where('name', 'like', '%' . $request->customer_name . '%');
        }
        if ($request->filled('customer_phone')) {
            $query->where('phone', 'like', '%' . $request->customer_phone . '%');
        }
        if ($request->filled('min_due')) {
            $query->where('due_amount', '>=', $request->min_due);
        }
        if ($request->filled('max_due')) {
            $query->where('due_amount', '<=', $request->max_due);
        }

        $query->where('due_amount', '>', 0);

        // Totals cover every customer that matches the filters, not just the current page
        $filteredDue = (clone $query)->sum('due_amount');
        $totalDue = Customer::where('due_amount', '>', 0)->sum('due_amount');
        $dueCustomerCount = Customer::where('due_amount', '>', 0)->count();
        $isFiltered = collect($request->only(['customer_name', 'customer_phone', 'min_due', 'max_due']))->filter(fn ($v) => filled($v))->isNotEmpty();

        $customers = $query->orderByDesc('id')->paginate(get_setting('pagination', 10))->withQueryString();

        return view('kazitds::due.index', compact('customers', 'filteredDue', 'totalDue', 'dueCustomerCount', 'isFiltered'));
    }

    // Show payment interface for a specific due
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $paymentHistories = PaymentHistory::where('customer_id', $customer->id)->orderByDesc('id')->paginate(get_setting('pagination', 10));
        return view('kazitds::due.payment', compact('customer', 'paymentHistories'));
    }

    // Store payment for a due
    public function storePayment(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        // Keep ledger state fresh before validating payment limit.
        app(CustomerSaleLedgerService::class)->recalculateForCustomer($customer->id);
        $customer->refresh();
        $currentDue = (float) $customer->due_amount;

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $currentDue,
            'payment_method' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $customer, $currentDue) {
            $paymentAmount = (float) $request->amount;
            $remaining = $paymentAmount;
            $epsilon = 0.0001;

            $sales = Sale::with('due')
                ->where('customer_id', $customer->id)
                ->orderBy('sale_date')
                ->orderBy('id')
                ->get();

            foreach ($sales as $sale) {
                $saleDue = (float) ($sale->due?->total_due ?? 0);
                if ($saleDue <= $epsilon) {
                    continue;
                }

                $appliedAmount = min($remaining, $saleDue);

                PaymentHistory::create([
                    'sale_id' => $sale->id,
                    'amount' => $appliedAmount,
                    'payment_method' => $request->payment_method,
                    'note' => $request->note,
                    'customer_id' => $customer->id,
                ]);

                $remaining -= $appliedAmount;
                if ($remaining <= $epsilon) {
                    break;
                }
            }

            $ledgerService = app(CustomerSaleLedgerService::class);

            if ($remaining > $epsilon) {
                // Fallback for customers that have manual due not tied to a sale.
                PaymentHistory::create([
                    'amount' => $remaining,
                    'payment_method' => $request->payment_method,
                    'note' => $request->note,
                    'customer_id' => $customer->id,
                ]);

                $targetDue = max(0, $currentDue - $paymentAmount);
                $ledgerService->applyManualDueAmount($customer->id, $targetDue);
                return;
            }

            $ledgerService->recalculateForCustomer($customer->id);
        });

        $customer->refresh();

        // Send SMS notification
        if (isset(get_setting('sms_permit')['payment']) && get_setting('sms_permit')['payment'] == 1) {
            if ($customer->due_amount > 0 && $request->input('sms')) {
                SmsNotifier::payment($customer, $currentDue, $request->amount, $customer->due_amount);
            }
        }

        return redirect()->route('due.index')->with('success', __('kazitds::kazitds.Payment successful.'));
    }

    public function notifyCustomer($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        if (isset(get_setting('sms_permit')['reminder']) && get_setting('sms_permit')['reminder'] == 1) {
            if ($customer->due_amount > 0) {
                SmsNotifier::notify($customer, $customer->due_amount);
            }
        }
        return redirect()->route('due.index')->with('success', __('kazitds::kazitds.Reminder sent successfully.'));
    }
}
