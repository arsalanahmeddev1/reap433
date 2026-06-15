<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
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
        $isAdmin = $user && $user->hasRole(config('roles.admin'));

        if (! $isAdmin) {
            return view('screens.admin.dashboard.admin', [
                'isAdmin' => false,
                'user' => $user,
                'stats' => [
                    'myOrdersCount' => Order::query()
                        ->where('customer_email', $user->email)
                        ->count(),
                ],
            ]);
        }

        $stats = [
            'totalUsers' => User::query()->where('role', config('roles.user', 'user'))->count(),
            'totalOrders' => Order::query()->count(),
            'pendingOrders' => Order::query()->where('status', 'pending_payment')->count(),
            'totalProducts' => Product::query()->count(),
            'totalCategories' => ProductCategory::query()->count(),
            'totalBlogs' => Blog::query()->count(),
            'totalRevenue' => (float) Order::query()
                ->where('status', '!=', 'cancelled')
                ->sum('subtotal'),
        ];

        $recentOrders = Order::query()
            ->latest()
            ->take(6)
            ->get();

        return view('screens.admin.dashboard.admin', [
            'isAdmin' => true,
            'user' => $user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
