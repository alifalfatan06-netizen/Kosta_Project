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
       // ANGGOTA 1 Mahendra (tabel utama CRUD Kamar)
Schema::create('kamar', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tipe_kamar_id')->constrained('tipe_kamar');
    $table->string('nomor_kamar')->unique();
    $table->unsignedTinyInteger('lantai')->default(1);
    $table->unsignedInteger('harga_sewa');
    $table->unsignedInteger('deposit');
    $table->text('fasilitas')->nullable();
    $table->string('foto')->nullable();
    $table->enum('status', ['tersedia', 'dibooking', 'terisi', 'nonaktif'])->default('tersedia');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
