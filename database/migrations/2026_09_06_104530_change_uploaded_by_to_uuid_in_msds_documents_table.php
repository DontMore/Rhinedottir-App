<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('msds_documents', function (Blueprint $table) {
            // Ubah tipe kolom dari integer ke uuid
            $table->uuid('uploaded_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('msds_documents', function (Blueprint $table) {
            // Kembalikan ke integer jika perlu rollback
            $table->unsignedBigInteger('uploaded_by')->nullable()->change();
        });
    }
};