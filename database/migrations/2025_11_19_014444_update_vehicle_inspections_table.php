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
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->string('pengembalian_kode')->nullable()->after('staff_id');
            
            // Menambahkan foreign key constraint
            $table->foreign('pengembalian_kode')
                  ->references('kode_pengembalian')
                  ->on('pengembalian')
                  ->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_inspections', function (Blueprint $table) {
            $table->dropForeign(['pengembalian_kode']);
            $table->dropColumn('pengembalian_kode');
        });
    }
};