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
        Schema::table('api_settings', function (Blueprint $table) {
            $table->json('selected_tables')->nullable()->after('push_interval');
        });
    }

    public function down()
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->dropColumn('selected_tables');
        });
    }
};
