<?php

namespace ME\Kazitds\Http\Controllers;

use ME\Http\Controllers\Controller;
use ME\Kazitds\Models\Pack;
use Illuminate\Http\Request;

class PackController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:pack.view')->only(['index', 'show']);
        $this->middleware('authorization:pack.create')->only(['create', 'store']);
        $this->middleware('authorization:pack.edit')->only(['edit', 'update']);
        $this->middleware('authorization:pack.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Pack::query();

        if ($request->has('name') && $request->input('name') != '') {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $packs = $query->paginate(get_setting('pagination', 10));
        $editItem = $request->filled('edit') ? Pack::find($request->input('edit')) : null;

        return view('kazitds::packs.index', compact('packs', 'editItem'));
    }

    public function create()
    {
        return redirect()->route('packs.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Pack::create($request->all());
        return redirect()->route('packs.index')
            ->with('success', __('kazitds::kazitds.Pack created successfully.'));
    }

    public function show(Pack $pack)
    {
        return view('kazitds::packs.show', compact('pack'));
    }

    public function edit(Pack $pack)
    {
        return redirect()->route('packs.index', ['edit' => $pack->id]);
    }

    public function update(Request $request, Pack $pack)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $pack->update($request->all());
        return redirect()->route('packs.index')
            ->with('success', __('kazitds::kazitds.Pack updated successfully.'));
    }

    public function destroy(Pack $pack)
    {
        if ($pack->productVariants()->count() > 0) {
            return redirect()->route('packs.index')
                ->withErrors(__('kazitds::kazitds.Cannot delete pack with existing products.'));
        }
        $pack->delete();
        return redirect()->route('packs.index')
            ->with('success', __('kazitds::kazitds.Pack deleted successfully.'));
    }
}
