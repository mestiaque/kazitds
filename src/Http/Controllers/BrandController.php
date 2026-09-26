<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:brand.view')->only(['index', 'show']);
        $this->middleware('authorization:brand.create')->only(['create', 'store']);
        $this->middleware('authorization:brand.edit')->only(['edit', 'update']);
        $this->middleware('authorization:brand.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->has('name') && $request->input('name') != '') {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $brands = $query->paginate(get_setting('pagination', 10));
        $editItem = $request->filled('edit') ? Brand::find($request->input('edit')) : null;

        return view('kazitds::brands.index', compact('brands', 'editItem'));
    }

    public function create()
    {
        return redirect()->route('brands.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Brand::create($request->all());
        return redirect()->route('brands.index')
            ->with('success', __('kazitds::kazitds.Brand created successfully.'));
    }

    public function show(Brand $brand)
    {
        return view('kazitds::brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return redirect()->route('brands.index', ['edit' => $brand->id]);
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand->update($request->all());
        return redirect()->route('brands.index')
            ->with('success', __('kazitds::kazitds.Brand updated successfully.'));
    }

    public function destroy(Brand $brand)
    {
        if ($brand->productVariants()->count() > 0) {
            return redirect()->route('brands.index')
                ->withErrors(__('kazitds::kazitds.Brand cannot be deleted because it has associated products.'));
        }
        $brand->delete();
        return redirect()->route('brands.index')
            ->with('success', __('kazitds::kazitds.Brand deleted successfully.'));
    }
}
