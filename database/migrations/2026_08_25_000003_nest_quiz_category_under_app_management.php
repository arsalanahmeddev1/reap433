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

        $quizCategory = CmsModule::updateOrCreate(
            ['route_name' => 'quiz-categories.index'],
            [
                'name' => 'Quiz Category',
                'icon' => 'fa-solid fa-book',
                'sort_order' => 1,
                'status' => 'active',
                'parent_id' => $parent->id,
            ]
        );

        CmsModulePermission::updateOrCreate(
            [
                'role' => 'admin',
                'module_id' => $quizCategory->id,
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
        $quizCategory = CmsModule::query()->where('route_name', 'quiz-categories.index')->first();
        if ($quizCategory) {
            $quizCategory->update([
                'parent_id' => 0,
                'sort_order' => 10,
            ]);
        }

        $parent = CmsModule::query()->where('route_name', 'app-management')->first();
        if ($parent) {
            CmsModulePermission::query()->where('module_id', $parent->id)->delete();
            $parent->delete();
        }
    }
};
