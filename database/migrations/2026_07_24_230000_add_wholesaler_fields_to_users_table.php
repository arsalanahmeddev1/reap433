<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('email');
            $table->string('business_phone')->nullable()->after('business_name');
            $table->string('business_email')->nullable()->after('business_phone');
            $table->string('business_location')->nullable()->after('business_email');
            $table->text('business_description')->nullable()->after('business_location');
            $table->string('approval_status')->default('approved')->after('role');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_phone',
                'business_email',
                'business_location',
                'business_description',
                'approval_status',
                'approved_at',
            ]);
        });
    }
};
