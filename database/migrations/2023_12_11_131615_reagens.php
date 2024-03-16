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
        Schema::create('reagens', function (Blueprint $table) {
            $table->string('noCatalog')->primary();
            $table->string('nameReagen');
            $table->string('merk');
            $table->string('packSize');
            $table->string('hazardOptions');
            $table->string('msds');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reagens');
    }
};
