<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $module = CmsModule::updateOrCreate(
            ['route_name' => 'coupons.index'],
            [
                'name' => 'Coupon Management',
                'icon' => 'fa-solid fa-ticket',
                'sort_order' => 6,
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
                'is_add' => 1,
                'is_update' => 1,
                'is_delete' => 1,
                'status' => 'active',
            ]
        );
    }

    public function down(): void
    {
        $module = CmsModule::query()->where('route_name', 'coupons.index')->first();

        if ($module) {
            CmsModulePermission::query()->where('module_id', $module->id)->delete();
            $module->delete();
        }
    }
};
