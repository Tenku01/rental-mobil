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
        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            // Hapus kolom peminjaman_id yang tidak relevan lagi
            $table->dropForeign(['peminjaman_id']);
            $table->dropColumn('peminjaman_id');

            // Tambahkan pengembalian_kode sebagai FK ke tabel pengembalian
            $table->string('pengembalian_kode')->after('mobil_id')->nullable();
            
            $table->foreign('pengembalian_kode')
                  ->references('kode_pengembalian')
                  ->on('pengembalian')
                  ->onDelete('cascade'); // Hapus laporan jika pengembalian dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            $table->dropForeign(['pengembalian_kode']);
            $table->dropColumn('pengembalian_kode');

            // Rollback: Tambahkan kembali peminjaman_id jika diperlukan
            $table->unsignedBigInteger('peminjaman_id')->nullable();
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->onDelete('set null');
        });
    }
};