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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('noCatalog');
            $table->string('nameReagen');
            $table->string('merk');
            $table->string('packSize');
            $table->integer('quantity');
            $table->unsignedBigInteger('userId'); // Menggunakan unsignedBigInteger untuk foreign key
            $table->boolean('status')->default(0); // Menggunakan boolean, bukan bolean
            $table->timestamps();

            // Tambahkan foreign key untuk userId
            $table->foreign('userId')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
