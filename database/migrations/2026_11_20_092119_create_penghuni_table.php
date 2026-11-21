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
        // ANGGOTA 2 Althaf
Schema::create('penghuni', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users');
    $table->string('nama');
    $table->string('no_hp', 20);
    $table->string('no_ktp', 20);
    $table->text('alamat')->nullable();
    $table->string('foto_ktp')->nullable();
    $table->enum('status', ['aktif', 'mantan'])->default('aktif');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghuni');
    }
};
