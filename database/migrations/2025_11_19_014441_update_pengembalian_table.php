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
        Schema::table('pengembalian', function (Blueprint $table) {
            // Hapus kolom denda yang akan dihitung dari relasi
            $table->dropColumn(['denda_kerusakan', 'denda_keterlambatan', 'total_denda']);
            
            // Hapus kolom yang dipindahkan ke VehicleInspection
            $table->dropColumn(['hasil_pengecekan', 'status_pemeriksaan']);
            
            // Opsional: Pastikan kolom 'status' ada dan cukup
            $table->string('status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            // Rollback: Tambahkan kembali kolom yang dihapus
            $table->decimal('denda_kerusakan', 10, 2)->nullable();
            $table->decimal('denda_keterlambatan', 10, 2)->nullable();
            $table->decimal('total_denda', 10, 2)->nullable();

            // Rollback: Tambahkan kembali kolom pengecekan
            $table->string('hasil_pengecekan')->nullable();
            $table->string('status_pemeriksaan')->nullable();
        });
    }
};