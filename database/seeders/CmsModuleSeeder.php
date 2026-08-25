<?php

namespace Database\Seeders;

use App\Models\CmsModule;
use Illuminate\Database\Seeder;

class CmsModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Uses updateOrCreate (not only firstOrCreate) so re-seeding repairs wrong parent_id
     * from old data where child route_names were left as top-level rows.
     *
     * Sidebar: only index / "all" listing routes — never create routes; add buttons live on those pages.
     */
    public function run(): void
    {
        $dashboard = CmsModule::updateOrCreate(
            ['route_name' => 'admin.dashboard'],
            [
                'name' => 'Dashboard',
                'icon' => 'fa-regular fa-house',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        /* One row only: same route_name must not be updateOrCreate’d twice (second pass set parent_id = own id → hidden from sidebar). */
        CmsModule::updateOrCreate(
            ['route_name' => 'users.index'],
            [
                'name' => 'Users',
                'icon' => 'fa-solid fa-users',
                'sort_order' => 2,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        $wholeSellerManagement = CmsModule::updateOrCreate(
            ['route_name' => 'whole-seller-management'],
            [
                'name' => 'Whole Seller Management',
                'icon' => 'fa-solid fa-handshake',
                'sort_order' => 3,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'whole-sellers.index'],
            [
                'name' => 'Whole Sellers',
                'icon' => 'fa-solid fa-store',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => $wholeSellerManagement->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'whole-seller-settings.index'],
            [
                'name' => 'Whole Seller Setting',
                'icon' => 'fa-solid fa-sliders',
                'sort_order' => 2,
                'status' => 'active',
                'parent_id' => $wholeSellerManagement->id,
            ]
        );

        $products = CmsModule::updateOrCreate(
            ['route_name' => 'products-module'],
            [
                'name' => 'Products',
                'icon' => 'fa-solid fa-box-open',
                'sort_order' => 3,
                'status' => 'inactive',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'admin.printful.products.index'],
            [
                'name' => 'Products',
                'icon' => 'fa-solid fa-box-open',
                'sort_order' => 3,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        /** Direct link to order list (same pattern as Dashboard → admin.dashboard). Not a fake "orders-module" slug. */
        CmsModule::updateOrCreate(
            ['route_name' => 'orders.index'],
            [
                'name' => 'Orders',
                'icon' => 'fa-solid fa-list-ul',
                'sort_order' => 4,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'abandoned-carts.index'],
            [
                'name' => 'Abandoned Carts',
                'icon' => 'fa-solid fa-cart-shopping',
                'sort_order' => 4,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'email-templates.index'],
            [
                'name' => 'Email templates',
                'icon' => 'fa-solid fa-envelope',
                'sort_order' => 5,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'coupons.index'],
            [
                'name' => 'Coupon Management',
                'icon' => 'fa-solid fa-ticket',
                'sort_order' => 6,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'product-categories.index'],
            [
                'name' => 'All Categories',
                'icon' => 'fa-solid fa-tags',
                'sort_order' => 1,
                'status' => 'inactive',
                'parent_id' => $products->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'products.index'],
            [
                'name' => 'All Products',
                'icon' => 'fa-solid fa-list-ul',
                'sort_order' => 2,
                'status' => 'inactive',
                'parent_id' => $products->id,
            ]
        );

        $blogs = CmsModule::updateOrCreate(
            ['route_name' => 'blogs-module'],
            [
                'name' => 'Blogs',
                'icon' => 'fa-solid fa-book-open',
                'sort_order' => 7,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'blog-categories.index'],
            [
                'name' => 'Blog categories',
                'icon' => 'fa-solid fa-tags',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => $blogs->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'blogs.index'],
            [
                'name' => 'All posts',
                'icon' => 'fa-solid fa-list-ul',
                'sort_order' => 2,
                'status' => 'active',
                'parent_id' => $blogs->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'collection-pages.index'],
            [
                'name' => 'Collection Pages',
                'icon' => 'fa-solid fa-layer-group',
                'sort_order' => 8,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'sitemaps.index'],
            [
                'name' => 'Sitemap',
                'icon' => 'fa-solid fa-sitemap',
                'sort_order' => 9,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        $appManagement = CmsModule::updateOrCreate(
            ['route_name' => 'app-management'],
            [
                'name' => 'App Management',
                'icon' => 'fa-solid fa-mobile',
                'sort_order' => 10,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'quiz-categories.index'],
            [
                'name' => 'Quiz Category',
                'icon' => 'fa-solid fa-book',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => $appManagement->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'quiz-types.index'],
            [
                'name' => 'Quiz Type',
                'icon' => 'fa-solid fa-layer-group',
                'sort_order' => 2,
                'status' => 'active',
                'parent_id' => $appManagement->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'quiz-questions.index'],
            [
                'name' => 'Quiz Question',
                'icon' => 'fa-solid fa-circle-question',
                'sort_order' => 3,
                'status' => 'active',
                'parent_id' => $appManagement->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'quiz-answers.index'],
            [
                'name' => 'Quiz Answer',
                'icon' => 'fa-solid fa-list-check',
                'sort_order' => 4,
                'status' => 'active',
                'parent_id' => $appManagement->id,
            ]
        );

        $allowed = [
            'admin.dashboard',
            'users.index',
            'whole-seller-management',
            'whole-sellers.index',
            'whole-seller-settings.index',
            'products-module',
            'product-categories.index',
            'products.index',
            'admin.printful.products.index',
            'orders.index',
            'abandoned-carts.index',
            'email-templates.index',
            'coupons.index',
            'blogs-module',
            'blog-categories.index',
            'blogs.index',
            'collection-pages.index',
            'sitemaps.index',
            'app-management',
            'quiz-categories.index',
            'quiz-types.index',
            'quiz-questions.index',
            'quiz-answers.index',
        ];

        CmsModule::query()
            ->where(function ($q) use ($allowed) {
                $q->whereNotIn('route_name', $allowed)
                    ->orWhereNull('route_name');
            })
            ->delete();
    }
}
