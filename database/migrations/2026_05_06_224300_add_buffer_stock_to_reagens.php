<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->integer('buffer_stock')->default(0)->after('price');
        });
    }

    public function down()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->dropColumn('buffer_stock');
        });
    }
};
