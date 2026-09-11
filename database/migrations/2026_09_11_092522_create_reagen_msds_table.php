<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reagen_msds', function (Blueprint $table) {
            $table->id();
            $table->uuid('reagen_guid');
            $table->string('file_name');           // Nama asli file (misal: MSDS_Acetone.pdf)
            $table->string('file_path');           // Path di storage
            $table->string('version')->nullable(); // Versi MSDS (misal: "Rev. A", "v1.0", "2024-01")
            $table->date('revision_date')->nullable(); // Tanggal revisi
            $table->text('notes')->nullable();     // Catatan tambahan
            $table->boolean('is_latest')->default(false); // Penanda versi terbaru
            $table->uuid('organization_guid');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('reagen_guid')
                  ->references('guid')
                  ->on('reagens')
                  ->onDelete('cascade');
                  
            $table->index(['reagen_guid', 'is_latest']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reagen_msds');
    }
};