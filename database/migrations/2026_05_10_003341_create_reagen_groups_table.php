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
        Schema::create('reagen_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('guid')->unique();
            $table->string('name', 100);
            $table->uuid('organization_guid');
            $table->timestamps();
            // Nama harus unik per organisasi
            $table->unique(['name', 'organization_guid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reagen_groups');
    }
};
