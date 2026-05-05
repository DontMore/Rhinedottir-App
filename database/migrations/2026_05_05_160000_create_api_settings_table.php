<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_settings', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->string('gas_web_app_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_settings');
    }
};
