<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->string('storage')->nullable()->after('packSize');
        });
    }

    public function down()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->dropColumn('storage');
        });
    }
};
