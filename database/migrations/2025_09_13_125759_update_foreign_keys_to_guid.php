<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update foreign keys to use GUIDs instead of old primary keys

        Schema::table('logbook_reagens', function (Blueprint $table) {
            $table->dropForeign(['noCatalog']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('noCatalog');
            $table->dropColumn('user_id');
            $table->uuid('reagen_guid');
            $table->uuid('user_guid');
            $table->foreign('reagen_guid')->references('guid')->on('reagens')->onDelete('cascade');
            $table->foreign('user_guid')->references('guid')->on('users')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');
            $table->uuid('user_guid');
            $table->foreign('user_guid')->references('guid')->on('users')->onDelete('cascade');
        });

        Schema::table('reagens_in', function (Blueprint $table) {
            $table->dropForeign(['noCatalog']);
            $table->dropColumn('noCatalog');
            $table->uuid('reagen_guid');
            $table->foreign('reagen_guid')->references('guid')->on('reagens')->onDelete('cascade');
        });

        Schema::table('stock_reagens', function (Blueprint $table) {
            $table->dropForeign(['noCatalog']);
            $table->dropColumn('noCatalog');
            $table->uuid('reagen_guid');
            $table->foreign('reagen_guid')->references('guid')->on('reagens')->onDelete('cascade');
        });

        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropForeign(['noCatalog']);
            $table->dropColumn('noCatalog');
            $table->uuid('reagen_guid');
            $table->foreign('reagen_guid')->references('guid')->on('reagens')->onDelete('cascade');
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['noCatalog']);
            $table->dropColumn('noCatalog');
            $table->uuid('reagen_guid');
            $table->foreign('reagen_guid')->references('guid')->on('reagens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse foreign key changes

        Schema::table('logbook_reagens', function (Blueprint $table) {
            $table->dropForeign(['reagen_guid']);
            $table->dropForeign(['user_guid']);
            $table->dropColumn('reagen_guid');
            $table->dropColumn('user_guid');
            $table->string('noCatalog');
            $table->unsignedBigInteger('user_id');
            $table->foreign('noCatalog')->references('noCatalog')->on('reagens')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_guid']);
            $table->dropColumn('user_guid');
            $table->unsignedBigInteger('userId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('reagens_in', function (Blueprint $table) {
            $table->dropForeign(['reagen_guid']);
            $table->dropColumn('reagen_guid');
            $table->string('noCatalog');
            $table->foreign('noCatalog')->references('noCatalog')->on('reagens')->onDelete('cascade');
        });

        Schema::table('stock_reagens', function (Blueprint $table) {
            $table->dropForeign(['reagen_guid']);
            $table->dropColumn('reagen_guid');
            $table->string('noCatalog');
            $table->foreign('noCatalog')->references('noCatalog')->on('reagens')->onDelete('cascade');
        });

        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropForeign(['reagen_guid']);
            $table->dropColumn('reagen_guid');
            $table->string('noCatalog');
            $table->foreign('noCatalog')->references('noCatalog')->on('reagens')->onDelete('cascade');
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['reagen_guid']);
            $table->dropColumn('reagen_guid');
            $table->string('noCatalog');
            $table->foreign('noCatalog')->references('noCatalog')->on('reagens')->onDelete('cascade');
        });
    }
};
