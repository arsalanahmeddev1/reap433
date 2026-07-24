<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $parent = CmsModule::updateOrCreate(
            ['route_name' => 'whole-seller-management'],
            [
                'name' => 'Whole Seller Management',
                'icon' => 'fa-solid fa-handshake',
                'sort_order' => 3,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModulePermission::updateOrCreate(
            [
                'role' => 'admin',
                'module_id' => $parent->id,
            ],
            [
                'is_view' => 1,
                'is_add' => 0,
                'is_update' => 0,
                'is_delete' => 0,
                'status' => 'active',
            ]
        );

        $wholeSellers = CmsModule::updateOrCreate(
            ['route_name' => 'whole-sellers.index'],
            [
                'name' => 'Whole Sellers',
                'icon' => 'fa-solid fa-store',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => $parent->id,
            ]
        );

        CmsModulePermission::updateOrCreate(
            [
                'role' => 'admin',
                'module_id' => $wholeSellers->id,
            ],
            [
                'is_view' => 1,
                'is_add' => 0,
                'is_update' => 1,
                'is_delete' => 0,
                'status' => 'active',
            ]
        );

        $settings = CmsModule::updateOrCreate(
            ['route_name' => 'whole-seller-settings.index'],
            [
                'name' => 'Whole Seller Setting',
                'icon' => 'fa-solid fa-sliders',
                'sort_order' => 2,
                'status' => 'active',
                'parent_id' => $parent->id,
            ]
        );

        CmsModulePermission::updateOrCreate(
            [
                'role' => 'admin',
                'module_id' => $settings->id,
            ],
            [
                'is_view' => 1,
                'is_add' => 0,
                'is_update' => 1,
                'is_delete' => 0,
                'status' => 'active',
            ]
        );
    }

    public function down(): void
    {
        $parent = CmsModule::query()->where('route_name', 'whole-seller-management')->first();

        foreach (['whole-sellers.index', 'whole-seller-settings.index'] as $routeName) {
            $child = CmsModule::query()->where('route_name', $routeName)->first();
            if ($child) {
                $child->update(['parent_id' => 0, 'sort_order' => $routeName === 'whole-sellers.index' ? 3 : 4]);
            }
        }

        if ($parent) {
            CmsModulePermission::query()->where('module_id', $parent->id)->delete();
            $parent->delete();
        }
    }
};
