<?php
// database/migrations/2025_11_07_000000_create_pembatalan_pesanan_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pembatalan_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // yang membatalkan
            $table->enum('cancelled_by', ['user','admin'])->default('user');
            $table->text('alasan')->nullable();
            $table->enum('refund_status', ['no_refund','pending_refund','refunded'])->default('no_refund');
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->timestamp('cancelled_at')->useCurrent();
            $table->timestamps();
        });
        // Optional: index status/cancelled_at jika perlu laporan
    }

    public function down(): void {
        Schema::dropIfExists('pembatalan_pesanan');
    }
};
