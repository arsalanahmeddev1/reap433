<?php

use App\Models\CmsModule;
use App\Models\CmsModulePermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sitemaps')) {
            Schema::create('sitemaps', function (Blueprint $table) {
                $table->id();
                $table->longText('content')->nullable();
                $table->timestamps();
            });

            DB::table('sitemaps')->insert([
                'content' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $module = CmsModule::updateOrCreate(
            ['route_name' => 'sitemaps.index'],
            [
                'name' => 'Sitemap',
                'icon' => 'fa-solid fa-sitemap',
                'sort_order' => 9,
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
        $module = CmsModule::query()->where('route_name', 'sitemaps.index')->first();

        if ($module) {
            CmsModulePermission::query()->where('module_id', $module->id)->delete();
            $module->delete();
        }

        Schema::dropIfExists('sitemaps');
    }
};
