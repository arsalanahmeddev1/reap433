<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class WholeSellerController extends Controller
{
    public function index()
    {
        $wholeSellers = User::query()
            ->where('role', config('roles.whole_seller', 'whole_seller'))
            ->orderByDesc('id')
            ->paginate(10);

        return view('screens.admin.whole-sellers.index', compact('wholeSellers'));
    }

    public function show(User $user)
    {
        abort_unless($user->isWholeSeller(), 404);

        $user->loadCount('orders');
        $user->load(['addresses' => fn ($query) => $query->orderByDesc('is_default')->latest()]);

        return view('screens.admin.whole-sellers.show', compact('user'));
    }

    public function approve(User $user)
    {
        abort_unless($user->isWholeSeller(), 404);

        if ($user->isApproved()) {
            return redirect()
                ->route('whole-sellers.show', $user)
                ->with('success', __('This whole seller account is already approved.'));
        }

        $user->markApproved();

        return redirect()
            ->route('whole-sellers.show', $user)
            ->with('success', __('Whole seller account approved. They can now sign in.'));
    }
}
