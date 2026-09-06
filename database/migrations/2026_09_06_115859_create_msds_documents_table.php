<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('msds_documents', function (Blueprint $table) {
            $table->id();
            
            // ✅ Foreign Key ke tabel reagens (menggunakan UUID sesuai struktur Reagen.php)
            $table->uuid('reagen_guid');
            $table->foreign('reagen_guid')
                  ->references('guid')
                  ->on('reagens')
                  ->cascadeOnDelete(); // Jika reagen dihapus, dokumen MSDS ikut terhapus

            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type', 10); // pdf, ppt, pptx
            $table->string('title')->nullable();
            
            // ✅ Kolom untuk menyimpan multiple trainer (JSON)
            $table->json('trainers')->nullable();

            // ✅ Foreign Key ke tabel users (LANGSUNG UUID agar tidak error tipe data)
            $table->uuid('uploaded_by')->nullable();
            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete(); // Jika user dihapus, nilai menjadi NULL

            $table->timestamps();

            // Index untuk mempercepat query pencarian berdasarkan reagen
            $table->index('reagen_guid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msds_documents');
    }
};