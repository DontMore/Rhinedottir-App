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
        Schema::create('logbook_reagens', function (Blueprint $table) {
            $table->id();
            $table->string('noCatalog'); // Ubah nama kolom ke bahasa Inggris
            $table->unsignedBigInteger('user_id'); // Ubah nama kolom ke bahasa Inggris
            $table->unsignedInteger('quantity_taken'); // Ubah nama kolom ke bahasa Inggris
            $table->string('note')->nullable();
            $table->timestamps();

            // Tambahkan foreign key untuk noCatalog
            $table->foreign('noCatalog')
                ->references('noCatalog')
                ->on('reagens')
                ->onDelete('cascade');

            // Tambahkan foreign key untuk userId
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_reagens');
    }
};
