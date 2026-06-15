<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        CmsModule::query()
            ->whereIn('route_name', ['products-module', 'product-categories.index', 'products.index'])
            ->update(['status' => 'inactive']);

        $printfulProducts = CmsModule::updateOrCreate(
            ['route_name' => 'admin.printful.products.index'],
            [
                'name' => 'Products',
                'icon' => 'fa-solid fa-box-open',
                'sort_order' => 3,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        $adminRole = config('roles.admin');

        if ($adminRole) {
            CmsModulePermission::updateOrCreate(
                [
                    'role' => $adminRole,
                    'module_id' => $printfulProducts->id,
                ],
                [
                    'is_view' => 1,
                    'is_add' => 0,
                    'is_update' => 0,
                    'is_delete' => 0,
                    'status' => 'active',
                ]
            );
        }
    }

    public function down(): void
    {
        CmsModule::query()
            ->where('route_name', 'admin.printful.products.index')
            ->delete();

        CmsModule::query()
            ->whereIn('route_name', ['products-module', 'product-categories.index', 'products.index'])
            ->update(['status' => 'active']);
    }
};
