<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop foreign key constraint lama
        DB::statement('ALTER TABLE msds_documents DROP FOREIGN KEY msds_documents_uploaded_by_foreign');
        
        // Step 2: Ubah tipe kolom dari integer ke UUID
        DB::statement('ALTER TABLE msds_documents MODIFY uploaded_by CHAR(36) NULL');
        
        // Step 3: Buat ulang foreign key constraint (jika users.id adalah UUID)
        // Hapus baris di bawah jika tabel users tidak menggunakan UUID
        DB::statement('
            ALTER TABLE msds_documents 
            ADD CONSTRAINT msds_documents_uploaded_by_foreign 
            FOREIGN KEY (uploaded_by) 
            REFERENCES users(id) 
            ON DELETE SET NULL
        ');
    }

    public function down(): void
    {
        // Rollback
        DB::statement('ALTER TABLE msds_documents DROP FOREIGN KEY msds_documents_uploaded_by_foreign');
        DB::statement('ALTER TABLE msds_documents MODIFY uploaded_by BIGINT UNSIGNED NULL');
        DB::statement('
            ALTER TABLE msds_documents 
            ADD CONSTRAINT msds_documents_uploaded_by_foreign 
            FOREIGN KEY (uploaded_by) 
            REFERENCES users(id) 
            ON DELETE SET NULL
        ');
    }
};