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
        Schema::create('ajuan_status_ajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuan_id')->unique()->constrained('ajuans')->onDelete('cascade');
            $table->foreignId('status_ajuan_id')->constrained('status_ajuans')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->date('realisasi')->nullable(); // tanggal realisasi yang diinput user
            $table->string('result_realisasi')->nullable(); // status realisasi: tercapai / telat / dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuan_status_ajuan');
    }
};
