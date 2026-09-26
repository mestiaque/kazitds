<?php
namespace ME\Kazitds\Http\Controllers;

use ME\Kazitds\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ME\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:product.view')->only(['index', 'show']);
        $this->middleware('authorization:product.create')->only(['create', 'store']);
        $this->middleware('authorization:product.edit')->only(['edit', 'update']);
        $this->middleware('authorization:product.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        $products = $query->paginate(get_setting('pagination', 10))
;

        $editItem = $request->filled('edit') ? Product::find($request->input('edit')) : null;

        return view('kazitds::products.index', compact('products', 'editItem'));
    }

    public function create()
    {
        return redirect()->route('products.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'bn_name' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:300',
            ]);

            $data = $request->all();

            // Check for existing product with same name
            $existingProduct = Product::where('name', $request->input('name'))->first();
            if ($existingProduct) {
                throw new \Exception(__('kazitds::kazitds.Product already exists.'));
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $imagePath = storage_path('app/public/images/products');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }

                $image->move($imagePath, $imageName);
                $data['image'] = $imageName;
            }

            Product::create($data);

            return redirect()->route('products.index')->with('success', __('kazitds::kazitds.Product added successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        return redirect()->route('products.index', ['edit' => $id]);
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'bn_name' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:300',
            ]);

            $data = $request->only(['name', 'bn_name']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Unlink old image if it exists
                if ($product->image && file_exists(storage_path('app/public/images/products/' . $product->image))) {
                    unlink(storage_path('app/public/images/products/' . $product->image));
                }

                $image = $request->file('image');
                $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $imagePath = storage_path('app/public/images/products');

                // Ensure the directory exists
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }

                $image->move($imagePath, $imageName);
                $data['image'] = $imageName;
            }

            $product->update($data);

            return redirect()->route('products.index')->with('success', __('kazitds::kazitds.Product updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            if($product->variants()->count() > 0) {
                return redirect()->back()->withErrors(__('kazitds::kazitds.Cannot delete product with existing variants.'));
            }
            // Delete the product image if it exists
            if ($product->image && file_exists(storage_path('app/public/images/products/' . $product->image))) {
                unlink(storage_path('app/public/images/products/' . $product->image));
            }

            $product->delete();

            return redirect()->route('products.index')->with('success', __('kazitds::kazitds.Product deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('kazitds::products.show', compact('product'));
    }
}
