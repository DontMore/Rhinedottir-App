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
        Schema::table('reagens', function (Blueprint $table) {
            $table->uuid('group_guid')->nullable()->after('buffer_stock');
            $table->uuid('category_guid')->nullable()->after('group_guid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reagens', function (Blueprint $table) {
            //
        });
    }
};
