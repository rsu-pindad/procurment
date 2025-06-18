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
        Schema::create('ajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('units_id')->nullable()->constrained('units', 'id');
            $table->date('tanggal_ajuan')->nullable(true);
            $table->string('produk_ajuan');
            $table->unsignedBigInteger('hps')->nullable(true)->default(0);
            $table->text('spesifikasi')->nullable(true);
            $table->string('file_rab')->nullable(true);
            $table->string('file_nota_dinas')->nullable(true);
            $table->string('file_analisa_kajian')->nullable(true);
            $table->string('jenis_ajuan');
            $table->dateTime('tanggal_update_terakhir');
            $table->foreignId('status_ajuans_id')->nullable(true)->constrained('status_ajuans', 'id');
            $table->foreignId('users_id')->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuans');
    }
};
