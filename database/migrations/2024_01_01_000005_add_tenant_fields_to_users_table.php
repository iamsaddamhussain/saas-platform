<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_super_admin')->default(false);
            $table->json('settings')->default('{}');
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('active')->default(true);
            
            $table->index(['tenant_id', 'active']);
            $table->index(['email', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'tenant_id', 'role_id', 'is_super_admin', 
                'settings', 'last_login_at', 'active'
            ]);
        });
    }
};