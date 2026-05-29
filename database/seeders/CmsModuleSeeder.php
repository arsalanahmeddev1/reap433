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

        $products = CmsModule::updateOrCreate(
            ['route_name' => 'products-module'],
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
            ['route_name' => 'product-categories.index'],
            [
                'name' => 'All Categories',
                'icon' => 'fa-solid fa-tags',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => $products->id,
            ]
        );

        CmsModule::updateOrCreate(
            ['route_name' => 'products.index'],
            [
                'name' => 'All Products',
                'icon' => 'fa-solid fa-list-ul',
                'sort_order' => 2,
                'status' => 'active',
                'parent_id' => $products->id,
            ]
        );

        $blogs = CmsModule::updateOrCreate(
            ['route_name' => 'blogs-module'],
            [
                'name' => 'Blogs',
                'icon' => 'fa-solid fa-book-open',
                'sort_order' => 5,
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

        $allowed = [
            'admin.dashboard',
            'users.index',
            'products-module',
            'product-categories.index',
            'products.index',
            'orders.index',
            'blogs-module',
            'blog-categories.index',
            'blogs.index',
        ];

        CmsModule::query()
            ->where(function ($q) use ($allowed) {
                $q->whereNotIn('route_name', $allowed)
                    ->orWhereNull('route_name');
            })
            ->delete();
    }
}
