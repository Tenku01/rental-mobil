<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ==========================================
        // LANGKAH 1: HAPUS DULU SEMUA FOREIGN KEY
        // ==========================================

        // Hapus FK di tabel peminjaman
        Schema::table('peminjaman', function (Blueprint $table) {
            // Laravel otomatis mencari constraint bernama 'peminjaman_mobil_id_foreign'
            $table->dropForeign(['mobil_id']); 
        });

        // Hapus FK di tabel vehicle_damage_reports
        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            $table->dropForeign(['mobil_id']);
        });

        // Hapus FK di tabel vehicle_inspections
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->dropForeign(['mobil_id']);
        });

        // ==========================================
        // LANGKAH 2: UBAH TIPE DATA KOLOM MENJADI STRING
        // ==========================================

        // 1. Ubah Parent (mobils)
        Schema::table('mobils', function (Blueprint $table) {
            $table->string('id', 20)->change();
        });

        // 2. Ubah Children
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('mobil_id', 20)->change();
        });

        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            $table->string('mobil_id', 20)->change();
        });

        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->string('mobil_id', 20)->change();
        });

        // ==========================================
        // LANGKAH 3: PASANG KEMBALI FOREIGN KEY
        // ==========================================

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('cascade');
        });

        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('cascade');
        });

        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('cascade');
        });
    }

    public function down()
    {
        // ==========================================
        // ROLLBACK: KEMBALIKAN KE BIG INTEGER
        // ==========================================

        // 1. Drop FK String
        Schema::table('peminjaman', function (Blueprint $table) { $table->dropForeign(['mobil_id']); });
        Schema::table('vehicle_damage_reports', function (Blueprint $table) { $table->dropForeign(['mobil_id']); });
        Schema::table('vehicle_inspections', function (Blueprint $table) { $table->dropForeign(['mobil_id']); });

        // 2. Ubah Tipe Data Kembali ke BigInteger
        Schema::table('mobils', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change(); // Auto increment mungkin perlu diset manual jika hilang
        });
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->change();
        });
        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->change();
        });
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->change();
        });

        // 3. Pasang FK Lama
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('cascade');
        });
        Schema::table('vehicle_damage_reports', function (Blueprint $table) {
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('cascade');
        });
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('cascade');
        });
    }
};