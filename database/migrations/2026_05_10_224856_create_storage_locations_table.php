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
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();
            $table->string('code', 50); // Contoh: A-01, LIQ-ACID-02
            $table->string('name', 100);
            $table->enum('state_type', ['solid', 'liquid']); // Padatan / Cairan
            $table->json('allowed_hazards')->nullable(); // ["Flammable", "Corrosive"]
            $table->integer('capacity')->nullable();
            $table->text('description')->nullable();
            $table->uuid('organization_guid');
            $table->timestamps();

            $table->unique(['code', 'organization_guid']);
            $table->index('organization_guid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_locations');
    }
};
