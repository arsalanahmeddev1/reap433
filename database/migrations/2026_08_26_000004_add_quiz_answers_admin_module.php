<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $parent = CmsModule::updateOrCreate(
            ['route_name' => 'app-management'],
            [
                'name' => 'App Management',
                'icon' => 'fa-solid fa-mobile',
                'sort_order' => 10,
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

        $module = CmsModule::updateOrCreate(
            ['route_name' => 'quiz-answers.index'],
            [
                'name' => 'Quiz Answer',
                'icon' => 'fa-solid fa-list-check',
                'sort_order' => 4,
                'status' => 'active',
                'parent_id' => $parent->id,
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
        $module = CmsModule::query()->where('route_name', 'quiz-answers.index')->first();

        if ($module) {
            CmsModulePermission::query()->where('module_id', $module->id)->delete();
            $module->delete();
        }
    }
};
