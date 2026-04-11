<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_add_is_active_to_users_table.php

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Opsi 1: Boolean (sederhana)
            $table->boolean('is_active')->default(true)->after('email');
            
            // Opsi 2: Enum (jika butuh lebih dari 2 status)
            // $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('email');
            
            // Opsional: tambahkan timestamp untuk pelacakan
            $table->timestamp('last_active_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'last_active_at']);
            // atau $table->dropColumn('status'); jika pakai enum
        });
    }
};
