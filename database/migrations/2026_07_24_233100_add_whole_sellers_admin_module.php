<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $module = CmsModule::updateOrCreate(
            ['route_name' => 'whole-sellers.index'],
            [
                'name' => 'Whole Sellers',
                'icon' => 'fa-solid fa-store',
                'sort_order' => 3,
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
                'is_update' => 1,
                'is_delete' => 0,
                'status' => 'active',
            ]
        );
    }

    public function down(): void
    {
        $module = CmsModule::query()->where('route_name', 'whole-sellers.index')->first();

        if ($module) {
            CmsModulePermission::query()->where('module_id', $module->id)->delete();
            $module->delete();
        }
    }
};
