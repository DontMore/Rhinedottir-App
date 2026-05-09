<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('order_recommendations', function (Blueprint $table) {
            $table->id();
            $table->uuid('organization_guid');
            $table->json('recommendations'); // Menyimpan array rekomendasi dalam format JSON
            $table->timestamp('generated_at');
            $table->timestamps();
            $table->index('organization_guid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_recommendations');
    }
};
