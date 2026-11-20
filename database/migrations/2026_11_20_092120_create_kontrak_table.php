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
        // ANGGOTA 2 (pusat database: kamar + penghuni + periode)
Schema::create('kontrak', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kamar_id')->constrained('kamar');
    $table->foreignId('penghuni_id')->constrained('penghuni');
    $table->date('tanggal_mulai');
    $table->date('tanggal_selesai');
    $table->unsignedSmallInteger('durasi_bulan');
    $table->unsignedInteger('harga_sewa'); // snapshot dari kamar
    $table->unsignedInteger('deposit');    // snapshot dari kamar
    $table->enum('status', ['aktif', 'selesai'])->default('aktif');
    $table->text('catatan')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak');
    }
};
