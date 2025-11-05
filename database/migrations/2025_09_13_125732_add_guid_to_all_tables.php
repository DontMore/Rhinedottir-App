<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // GUID already added to users and organizations tables in previous migrations

        // Add GUID to reagens table
        Schema::table('reagens', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE reagens SET guid = UUID() WHERE guid IS NULL');
        Schema::table('reagens', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to logbook_reagens table
        Schema::table('logbook_reagens', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE logbook_reagens SET guid = UUID() WHERE guid IS NULL');
        Schema::table('logbook_reagens', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE orders SET guid = UUID() WHERE guid IS NULL');
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to reagens_in table
        Schema::table('reagens_in', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE reagens_in SET guid = UUID() WHERE guid IS NULL');
        Schema::table('reagens_in', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to stock_reagens table
        Schema::table('stock_reagens', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE stock_reagens SET guid = UUID() WHERE guid IS NULL');
        Schema::table('stock_reagens', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to email_settings table
        Schema::table('email_settings', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE email_settings SET guid = UUID() WHERE guid IS NULL');
        Schema::table('email_settings', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to stock_histories table
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE stock_histories SET guid = UUID() WHERE guid IS NULL');
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });

        // Add GUID to stock_opnames table
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->uuid('guid')->unique()->nullable();
        });
        DB::statement('UPDATE stock_opnames SET guid = UUID() WHERE guid IS NULL');
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->uuid('guid')->unique()->change();
            $table->dropPrimary();
            $table->primary('guid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse GUID changes - drop guid and restore old primary keys
        // GUID for users and organizations reversed in previous migrations

        Schema::table('reagens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->string('noCatalog')->primary()->first();
        });

        Schema::table('logbook_reagens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id()->first();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id()->first();
        });

        Schema::table('reagens_in', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id('Id')->first();
        });

        Schema::table('stock_reagens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id('stockId')->first();
        });

        Schema::table('email_settings', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id()->first();
        });

        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id('Id')->first();
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('guid');
            $table->id()->first();
        });
    }
};
