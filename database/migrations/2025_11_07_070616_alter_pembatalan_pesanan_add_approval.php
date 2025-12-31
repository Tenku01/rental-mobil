<?php

// database/migrations/2025_11_07_100001_alter_pembatalan_pesanan_add_approval.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pembatalan_pesanan', function (Blueprint $table) {
            $table->enum('approval_status', ['pending','approved','rejected'])->default('pending')->after('cancelled_by');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void {
        Schema::table('pembatalan_pesanan', function (Blueprint $table) {
            $table->dropColumn(['approval_status','reviewed_by','review_note']);
        });
    }
};
