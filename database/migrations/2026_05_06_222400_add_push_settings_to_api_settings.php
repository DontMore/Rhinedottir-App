<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->boolean('push_is_active')->default(false)->after('is_active');
            $table->string('push_interval')->default('daily')->after('push_is_active');
            $table->timestamp('last_push_at')->nullable()->after('push_interval');
        });
    }

    public function down()
    {
        Schema::table('api_settings', function (Blueprint $table) {
            $table->dropColumn(['push_is_active', 'push_interval', 'last_push_at']);
        });
    }
};
