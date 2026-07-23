<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $module = CmsModule::updateOrCreate(
            ['route_name' => 'abandoned-carts.index'],
            [
                'name' => 'Abandoned Carts',
                'icon' => 'fa-solid fa-cart-shopping',
                'sort_order' => 4,
                'status' => 'active',
                'parent_id' => 0,
            ]
        );

        CmsModulePermission::updateOrCreate(
            [
                'role' => 'admin',
                'module_id' => $module->id,
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

    public function down(): void
    {
        $module = CmsModule::query()->where('route_name', 'abandoned-carts.index')->first();

        if ($module) {
            CmsModulePermission::query()->where('module_id', $module->id)->delete();
            $module->delete();
        }
    }
};
