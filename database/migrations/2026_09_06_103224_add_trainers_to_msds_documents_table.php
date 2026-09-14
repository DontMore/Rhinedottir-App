<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('msds_documents', function (Blueprint $table) {
            $table->json('trainers')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('msds_documents', function (Blueprint $table) {
            $table->dropColumn('trainers');
        });
    }
};