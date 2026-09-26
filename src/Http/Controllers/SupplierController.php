<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:supplier.view')->only(['index', 'show']);
        $this->middleware('authorization:supplier.create')->only(['create', 'store']);
        $this->middleware('authorization:supplier.edit')->only(['edit', 'update']);
        $this->middleware('authorization:supplier.delete')->only('destroy');
        $this->middleware('authorization:supplier.print')->only('printInvoice');
    }

    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('name') && $request->input('name') != '') {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('phone') && $request->input('phone') != '') {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        $suppliers = $query->paginate(get_setting('pagination', 10));

        $editItem = $request->filled('edit') ? Supplier::find($request->input('edit')) : null;

        return view('kazitds::suppliers.index', compact('suppliers', 'editItem'));
    }

    public function create()
    {
        return redirect()->route('suppliers.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Supplier::create($request->all());
        return redirect()->route('suppliers.index')
            ->with('success', __('kazitds::kazitds.Supplier created successfully.'));
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('purchases.productVariant.product', 'purchases.productVariant.brand');
        return view('kazitds::suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return redirect()->route('suppliers.index', ['edit' => $supplier->id]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')
            ->with('success', __('kazitds::kazitds.Supplier updated successfully.'));
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->withErrors(__('kazitds::kazitds.Cannot delete supplier with existing purchases.'));
        }
        $supplier->delete();
        return redirect()->route('suppliers.index')
            ->with('success', __('kazitds::kazitds.Supplier deleted successfully.'));
    }
}
