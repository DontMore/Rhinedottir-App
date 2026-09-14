<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('msds_documents', function (Blueprint $table) {
            $table->id();
            
            // ✅ PERBAIKAN: Gunakan reagen_guid (string UUID), BUKAN reagen_id (integer)
            // Merujuk ke kolom 'guid' di tabel 'reagens'
            $table->uuid('reagen_guid');
            $table->foreign('reagen_guid')
                  ->references('guid')
                  ->on('reagens')
                  ->cascadeOnDelete();
            
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type', 10); // pdf, ppt, pptx
            $table->string('title')->nullable();
            
            // ✅ PERBAIKAN: uploaded_by juga harus uuid jika users pakai uuid
            // Jika tabel users masih pakai id integer, biarkan seperti ini:
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('reagen_guid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('msds_documents');
    }
};