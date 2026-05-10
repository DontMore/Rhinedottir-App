<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->uuid('storage_location_guid')->nullable()->after('buffer_stock');
            $table->text('recommended_storage_location')->nullable()->after('storage_location_guid');
            
            $table->foreign('storage_location_guid')
                  ->references('guid')->on('storage_locations')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reagens', function (Blueprint $table) {
            $table->dropForeign(['storage_location_guid']);
            $table->dropColumn(['storage_location_guid', 'recommended_storage_location']);
        });
    }
};