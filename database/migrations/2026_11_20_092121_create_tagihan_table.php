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
        // ANGGOTA 3 Alip  |  total = nominal + biaya_listrik + denda
Schema::create('tagihan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kontrak_id')->constrained('kontrak');
    $table->enum('jenis', ['deposit', 'sewa']);
    $table->string('periode', 7)->nullable();   // format Y-m
    $table->date('jatuh_tempo');
    $table->unsignedInteger('nominal');
    $table->unsignedInteger('meter_awal')->default(0);
    $table->unsignedInteger('meter_akhir')->nullable();
    $table->unsignedInteger('tarif_listrik')->default(0);
    $table->unsignedInteger('biaya_listrik')->default(0);
    $table->unsignedInteger('denda')->default(0);
    $table->unsignedInteger('total')->default(0);
    $table->unsignedInteger('jumlah_dibayar')->default(0);
    $table->enum('status', ['belum_bayar', 'sebagian', 'lunas'])->default('belum_bayar');
    $table->date('tanggal_bayar')->nullable();
    $table->string('catatan')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
