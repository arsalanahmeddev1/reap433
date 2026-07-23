<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::query()
            ->latest()
            ->get();

        return view('screens.admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPayload($request);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('coupons', 'public');
        }

        Coupon::create([
            'title' => $validated['title'],
            'slug' => Coupon::slugFromTitle($validated['title']),
            'coupon_code' => $validated['coupon_code'],
            'image' => $imagePath,
            'description' => $validated['description'],
            'discount_in_percent' => $validated['discount_in_percent'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', __('Coupon created successfully.'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $this->validatedPayload($request, $coupon);

        $imagePath = $coupon->image;

        if ($request->boolean('remove_image') && $coupon->image) {
            Storage::disk('public')->delete($coupon->image);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($coupon->image) {
                Storage::disk('public')->delete($coupon->image);
            }
            $imagePath = $request->file('image')->store('coupons', 'public');
        }

        $coupon->update([
            'title' => $validated['title'],
            'slug' => Coupon::slugFromTitle($validated['title'], $coupon->id),
            'coupon_code' => $validated['coupon_code'],
            'image' => $imagePath,
            'description' => $validated['description'],
            'discount_in_percent' => $validated['discount_in_percent'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', __('Coupon updated successfully.'));
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with('success', __('Coupon deleted successfully.'));
    }

    /**
     * @return array{title: string, coupon_code: string, description: ?string, discount_in_percent: int, status: string}
     */
    private function validatedPayload(Request $request, ?Coupon $coupon = null): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'coupon_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('coupons', 'coupon_code')->ignore($coupon?->id)->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'discount_in_percent' => 'required|integer|min:1|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:4096',
            'remove_image' => 'sometimes|boolean',
            'status' => 'required|string|in:active,inactive',
        ]);

        return [
            'title' => $validated['title'],
            'coupon_code' => strtoupper(trim($validated['coupon_code'])),
            'description' => $validated['description'] ?? null,
            'discount_in_percent' => (int) $validated['discount_in_percent'],
            'status' => $validated['status'],
        ];
    }
}
