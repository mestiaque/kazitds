<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Customer;
use Illuminate\Http\Request;
use ME\Kazitds\Http\Services\CustomerSaleLedgerService;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:customer.view')->only(['index', 'show']);
        $this->middleware('authorization:customer.create')->only(['create', 'store']);
        $this->middleware('authorization:customer.edit')->only(['edit', 'update']);
        $this->middleware('authorization:customer.delete')->only('destroy');
    }

    public function index(Request $request)
    {

        $query = Customer::query();

        if ($request->has('name') && $request->input('name') != '') {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('phone') && $request->input('phone') != '') {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        $customers = $query->paginate(get_setting('pagination', 10));


        $editItem = $request->filled('edit') ? Customer::find($request->input('edit')) : null;

        return view('kazitds::customers.index', compact('customers', 'editItem'));
    }

    public function create()
    {
        return redirect()->route('customers.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'due_amount' => 'nullable|numeric|min:0',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', __('kazitds::kazitds.Customer created successfully.'));
    }

    public function show(Customer $customer)
    {
        // যদি sales লোড করতে চাও:
        $customer->load('sales');
        return view('kazitds::customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return redirect()->route('customers.index', ['edit' => $customer->id]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'due_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $targetDueAmount = (float) ($validated['due_amount'] ?? 0);
            $customer->update($validated);

            app(CustomerSaleLedgerService::class)->applyManualDueAmount($customer->id, $targetDueAmount);
        });

        return redirect()->route('customers.index')
            ->with('success', __('kazitds::kazitds.Customer updated successfully.'));
    }

    public function destroy(Customer $customer)
    {
        // যদি কোনো sales থাকে তাহলে ডিলেট না করতে চাও:
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')
                ->withErrors(__('kazitds::kazitds.Cannot delete customer with existing sales.'));
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', __('kazitds::kazitds.Customer deleted successfully.'));
    }
}
