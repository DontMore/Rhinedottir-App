<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->string('api_token', 80)->unique()->nullable()->after('guid');
        });
    }

    public function down()
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
};
