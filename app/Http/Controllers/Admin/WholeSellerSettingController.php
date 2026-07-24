<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WholeSellerSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WholeSellerSettingController extends Controller
{
    public function index(): View
    {
        $setting = WholeSellerSetting::current();

        return view('screens.admin.whole-seller-settings.index', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_discount' => ['required', 'integer', 'min:1', 'max:100'],
            'order_quantity' => ['required', 'integer', 'min:1'],
        ], [
            'product_discount.required' => __('Please select a product discount.'),
            'product_discount.min' => __('Product discount must be between 1 and 100.'),
            'product_discount.max' => __('Product discount must be between 1 and 100.'),
            'order_quantity.required' => __('Order quantity is required.'),
            'order_quantity.min' => __('Order quantity must be at least 1.'),
        ]);

        $setting = WholeSellerSetting::current();
        $setting->update([
            'product_discount' => $validated['product_discount'],
            'order_quantity' => $validated['order_quantity'],
        ]);

        return redirect()
            ->route('whole-seller-settings.index')
            ->with('success', __('Whole seller setting updated successfully.'));
    }
}
