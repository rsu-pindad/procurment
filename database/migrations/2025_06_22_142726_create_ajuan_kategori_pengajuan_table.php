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
        Schema::create('ajuan_kategori_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_id')->constrained('ajuans')->onDelete('cascade');
            $table->foreignId('kategori_pengajuan_id')->constrained('kategori_pengajuans')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuan_kategori_pengajuan');
    }
};
