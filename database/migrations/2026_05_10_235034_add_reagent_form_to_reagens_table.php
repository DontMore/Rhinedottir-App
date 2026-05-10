<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->enum('reagent_form', ['liquid', 'solid', 'crystal'])->nullable()->after('buffer_stock');
        });
    }

    public function down()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->dropColumn('reagent_form');
        });
    }
};