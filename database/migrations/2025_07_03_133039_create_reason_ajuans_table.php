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
        Schema::create('reason_ajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_id')->constrained('ajuans')->onDelete('cascade');
            $table->foreignId('status_ajuan_id')->constrained('status_ajuans')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('reason_keterangan_ajuan')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reason_ajuans');
    }
};
