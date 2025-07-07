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
        Schema::table('ajuans', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('users_id')->constrained('vendors')->onDelete('cascade');
            $table->unsignedBigInteger('hps_nego')->nullable()->default(0)->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajuans', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
            $table->dropColumn('hps_nego');
        });
    }
};
