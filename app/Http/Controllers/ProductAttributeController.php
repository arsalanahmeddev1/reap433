<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $variations = ProductAttribute::query()->latest()->get();

        return view('screens.admin.product-variations.index', compact('variations'));
    }

    public function create()
    {
        return view('screens.admin.product-variations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
        ]);

        ProductAttribute::create([
            'name' => trim($data['name']),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variation created successfully',
            'redirect' => route('product-variations.index'),
        ]);
    }
}
