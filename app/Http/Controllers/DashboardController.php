<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $isAdmin = $user && $user->role === config('roles.admin');

        if ($isAdmin) {
            $stats = [
                'totalUsers' => User::query()->count(),
                'totalOrders' => Order::query()->count(),
                'totalProducts' => Product::query()->count(),
                'totalCategories' => ProductCategory::query()->count(),
            ];

            return view('screens.admin.dashboard.admin', [
                'isAdmin' => true,
                'stats' => $stats,
            ]);
        }

        return view('screens.admin.dashboard.admin', [
            'isAdmin' => false,
            'stats' => [
                'myOrdersCount' => Order::query()->where('user_id', $user->id)->count(),
            ],
        ]);
    }
}
