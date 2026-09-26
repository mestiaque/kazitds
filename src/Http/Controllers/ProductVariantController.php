<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Pack;
use ME\Kazitds\Models\Brand;
use ME\Kazitds\Models\Product;
use Illuminate\Http\Request;
use ME\Kazitds\Models\ProductVariant;

class ProductVariantController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:product_variant.view')->only(['index', 'show']);
        $this->middleware('authorization:product_variant.create')->only(['create', 'store']);
        $this->middleware('authorization:product_variant.edit')->only(['edit', 'update']);
        $this->middleware('authorization:product_variant.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'brand', 'pack']);

        // Filter by product if requested
        if ($request->has('product') && $request->product) {
            $query->where('product_id', $request->product);
        }

        // Filter by brand if requested
        if ($request->has('brand') && $request->brand) {
            $query->where('brand_id', $request->brand);
        }

        // Filter by pack if requested
        if ($request->has('pack') && $request->pack) {
            $query->where('pack_id', $request->pack);
        }

        $productVariants = $query->paginate(get_setting('pagination', 10));

        // Get all products, brands and packs for the filters
        $products = Product::all();
        $brands = Brand::all();
        $packs = Pack::all();

        $editItem = $request->filled('edit') ? ProductVariant::find($request->input('edit')) : null;

        return view('kazitds::product_variants.index', compact('productVariants', 'products', 'brands', 'packs', 'editItem'));
    }

    public function create()
    {
        return redirect()->route('product-variants.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'brand_id' => 'nullable|exists:brands,id',
            'pack_id' => 'required|exists:packs,id',
            'is_active' => 'in:on,off',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') && $request->is_active === 'on';

        ProductVariant::create($data);
        return redirect()->route('product-variants.index')
            ->with('success', __('kazitds::kazitds.Product variant created successfully.'));
    }

    public function show(ProductVariant $productVariant)
    {
        $productVariant->load('product', 'brand', 'pack');
        return view('kazitds::product_variants.show', compact('productVariant'));
    }

    public function edit(ProductVariant $productVariant)
    {
        return redirect()->route('product-variants.index', ['edit' => $productVariant->id]);
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'brand_id' => 'nullable|exists:brands,id',
            'pack_id' => 'required|exists:packs,id',
            'is_active' => 'in:on,off',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') && $request->is_active === 'on';

        $productVariant->update($data);
        return redirect()->route('product-variants.index')
            ->with('success', __('kazitds::kazitds.Product variant updated successfully.'));
    }

    public function destroy(ProductVariant $productVariant)
    {
        if ($productVariant->purchases()->count() > 0) {
            return redirect()->route('product-variants.index')
                ->withErrors(__('kazitds::kazitds.Cannot delete product variant with associated orders.'));
        }
        $productVariant->delete();
        return redirect()->route('product-variants.index')
            ->with('success', __('kazitds::kazitds.Product variant deleted successfully.'));
    }
}
