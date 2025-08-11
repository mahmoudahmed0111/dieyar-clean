<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::orderByDesc('created_at')->paginate(15);
        return view('dashboard.inventory.index', compact('items'));
    }

    public function create()
    {
        return view('dashboard.inventory.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|string|max:255',
            'quantity' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }
        Inventory::create($data);
        return redirect()->route('dashboard.inventory.index')->with('success', __('Item added successfully.'));
    }

    public function edit(Inventory $inventory)
    {
        return view('dashboard.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|string|max:255',
            'quantity' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }
        $inventory->update($data);
        return redirect()->route('dashboard.inventory.index')->with('success', __('Item updated successfully.'));
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('dashboard.inventory.index')->with('success', __('Item deleted successfully.'));
    }
}
