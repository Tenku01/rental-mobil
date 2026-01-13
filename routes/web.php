<?php

use App\Models\Resepsionis;
use App\Livewire\admin\HomeIndex;
use App\Livewire\admin\RoleIndex;
use App\Livewire\admin\UserIndex;
use App\Livewire\admin\MobilIndex;
use App\Livewire\admin\SopirIndex;
use App\Livewire\admin\StaffIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\admin\PeminjamanIndex;
use App\Livewire\admin\ResepsionisIndex;
use App\Livewire\admin\PengembalianIndex;
use App\Livewire\admin\DriverLogbookIndex;
use App\Livewire\admin\VehicleDamageIndex;
use App\Http\Controllers\LandingController;
use App\Livewire\admin\VerifikasiUserIndex;
use App\Livewire\admin\PembatalanPesananIndex;
use App\Livewire\admin\VehicleInspectionIndex;
use App\Http\Controllers\Sopir\LogbookController;
use App\Http\Controllers\User\IdentityController;
use App\Http\Controllers\User\MidtransController;
use App\Http\Controllers\User\MobilControllerUser;
use App\Http\Controllers\User\PeminjamanController;
use App\Http\Controllers\Resepsionis\FineController;
use App\Http\Controllers\Resepsionis\UserController;
use App\Http\Controllers\Sopir\SopirActionController;
use App\Http\Controllers\User\PengembalianController;
use App\Http\Controllers\Sopir\SopirDashboardController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Resepsionis\DashboardController;
use App\Http\Controllers\Resepsionis\PelangganController;
use App\Http\Controllers\User\PembatalanPesananController;
use App\Http\Controllers\Resepsionis\TransactionController;
use App\Http\Controllers\Resepsionis\VerificationController;
use App\Http\Controllers\Admin\PembatalanPesananApprovalController;
use App\Http\Controllers\Resepsionis\MobilController as ResepsionisMobilController;
use App\Http\Controllers\Resepsionis\MidtransController as ResepsionisMidtransController;
use App\Http\Controllers\Resepsionis\PeminjamanController as ResepsionisPeminjamanController;
use App\Http\Controllers\Resepsionis\PengembalianController as ResepsionisPengembalianController;
use App\Http\Controllers\Resepsionis\PembatalanPesananController as ResepsionisPembatalanPesananController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Auth routes (login, register, forgot password, dll)
require __DIR__ . '/auth.php';

// Hanya untuk user yang login
Route::middleware(['auth'])->group(function () {

    // Dashboard user
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    // Mobil (User)
    Route::get('/mobils', [MobilControllerUser::class, 'mobil'])->name('mobils.index');
    Route::get('/mobils/{id}', [MobilControllerUser::class, 'show'])->name('mobils.show');

    // Identitas / Profil
    Route::get('user/upload-identity', [IdentityController::class, 'showUploadForm'])->name('upload.identity');
    Route::post('user/upload-identity', [IdentityController::class, 'store']);
    Route::get('user/edit-identity/{id}', [IdentityController::class, 'edit'])->name('edit.identity');
    Route::post('user/edit-identity/{id}', [IdentityController::class, 'update'])->name('update.identity');
    Route::delete('user/delete-identity/{id}', [IdentityController::class, 'destroy'])->name('delete.identity');

    // Pesanan & Peminjaman
    // Route::get('/pesanan-saya', [PeminjamanController::class, 'pesananSaya'])->name('pesanan.index');
    Route::get('/peminjaman/{mobil_id}', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('/check-driver-availability', [PeminjamanController::class, 'checkDriver'])
        ->name('check.driver');
    // Midtrans Payment Integration
    Route::get('/pesanan-saya', [PeminjamanController::class, 'index'])->name('pesanan.saya');

    // Cek kondisi mobil sebelum pengembalian
    Route::post('/pesanan/{peminjaman}/cek-kondisi', [PeminjamanController::class, 'cekKondisi'])->name('peminjaman.cek-kondisi');


    // 🔹 Route Midtrans (sudah ada)
    Route::get('/peminjaman/{peminjaman}/pay', [MidtransController::class, 'pay'])->name('payment.pay');
    Route::get('/peminjaman/{peminjaman}/pay-sisa', [MidtransController::class, 'paySisa'])->name('payment.pay-sisa');
    Route::post('/payment/notification', [MidtransController::class, 'notification'])->name('payment.notification');
    Route::get('/payment/callback', [MidtransController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/success', [MidtransController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed', [MidtransController::class, 'failed'])->name('payment.failed');
    Route::get('/payment/unfinish', [MidtransController::class, 'unfinish'])->name('payment.unfinish');
    // 🔹 Batalkan transaksi jika Snap ditutup tanpa pembayaran
    Route::delete('/peminjaman/{peminjaman}/cancel-payment', [MidtransController::class, 'cancelPayment'])
        ->name('peminjaman.cancel-payment');
    Route::delete('/peminjaman/{id}/cancel', [PeminjamanController::class, 'cancel'])
        ->name('peminjaman.cancel');

    //pengembalian
    // Route::post('/peminjaman/{id}/pengembalian', [PeminjamanController::class, 'prosesPengembalian'])
    //     ->name('peminjaman.pengembalian');
    // Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'store'])->name('pengembalian.store');
    Route::post('/pengembalian/pengecekan/{id}', [PengembalianController::class, 'pengecekan'])->name('pengembalian.pengecekan');
    Route::post('/pengembalian/bayar/{id}', [PengembalianController::class, 'bayarDenda'])->name('pengembalian.bayar');
    Route::post('/peminjaman/{peminjaman}/cancel', [PembatalanPesananController::class, 'store'])
        ->name('pembatalan.store');

    Route::post('/peminjaman/{peminjaman_id}/kembalikan', [PengembalianController::class, 'store'])
        ->name('pengembalian.store');

    // Melihat Status & Detail Pengembalian (Step 5-6)
    Route::get('/pengembalian/{kode_pengembalian}', [PengembalianController::class, 'show'])
        ->name('pengembalian.show');

    // Inisiasi Pembayaran Midtrans (Step 7-8)
    Route::post('/pengembalian/{kode_pengembalian}/snap-token', [PengembalianController::class, 'generateSnapToken'])
        ->name('pengembalian.generateSnapToken');

    // Memilih Metode Pembayaran Manual/Tunai (Step 7, Aksi Tunai/Transfer)
    Route::post('/pengembalian/{kode_pengembalian}/select-manual-payment', [PengembalianController::class, 'selectManualPaymentMethod'])
        ->name('pengembalian.selectManualPaymentMethod');

    // // Route dari definisi lama Anda (jika masih diperlukan)
    // Route::post('/peminjaman/{peminjaman}/cancel', [PembatalanPesananController::class, 'store'])
    //     ->name('pembatalan.store');

    //Profile
    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});

// Route untuk menampilkan daftar pengecekan (GET)
Route::middleware(['auth', 'staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        // 🔹 Dashboard staff umum
        Route::get('/dashboard', [StaffDashboardController::class, 'dashboard'])
            ->name('dashboard');

        // 🔹 Profile staff
        Route::view('/profile', 'staff.profile')
            ->name('profile');

        // --- Alur Pengecekan ---

        // 🔹 1. Halaman utama pengecekan (Form Pencarian)
        Route::get('/pengecekan', [StaffDashboardController::class, 'index'])
            ->name('pengecekan.index');
        Route::get('/pengecekan/{kode_pengembalian}', [StaffDashboardController::class, 'cek'])
            ->where('kode_pengembalian', '[A-Za-z0-9\-]+')
            ->name('pengecekan.view');


        // 🔹 2. Aksi ketika tombol "Cek" disubmit (Menampilkan Detail)
        Route::post('/pengecekan/{kode_pengembalian}/cek', [StaffDashboardController::class, 'cek'])
            ->where('kode_pengembalian', '[A-Za-z0-9\-]+')
            ->name('pengecekan.cek');

        // 🔹 3. Aksi Finalisasi (Menyimpan semua hasil pengecekan)
        Route::post('/pengecekan/{kode_pengembalian}/finalisasi', [StaffDashboardController::class, 'finalisasiPengecekan'])
            ->where('kode_pengembalian', '[A-Za-z0-9\-]+')
            ->name('pengecekan.finalisasi');

        Route::get(
            '/pengecekan/{kode_pengembalian}/detail',
            [StaffDashboardController::class, 'detail']
        )->name('pengecekan.detail');
    });


Route::middleware(['auth', 'resepsionis'])
    ->prefix('resepsionis')
    ->name('resepsionis.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('pelanggan', PelangganController::class);

        Route::resource('mobil', ResepsionisMobilController::class)->except(['destroy']);

        // Pelanggan edit/update rute (sudah spesifik)
        Route::get('/pelanggan/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('/pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');

        Route::resource('peminjaman', ResepsionisPeminjamanController::class);

        // Peminjaman Show (Rute ini harusnya berada di resource peminjaman di atas)
        Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');

        // 🟢 Rute Pembatalan Pesanan (Dibersihkan dan Dikelompokkan)
        Route::prefix('pembatalan')->name('pembatalan.')->group(function () {
            // CRUD Standar (Index, Create, Store, Show, Edit, Update)
            Route::get('/', [ResepsionisPembatalanPesananController::class, 'index'])->name('index');
            Route::get('/create', [ResepsionisPembatalanPesananController::class, 'create'])->name('create');
            Route::post('/', [ResepsionisPembatalanPesananController::class, 'store'])->name('store');
            Route::get('/{id}', [ResepsionisPembatalanPesananController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ResepsionisPembatalanPesananController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ResepsionisPembatalanPesananController::class, 'update'])->name('update');

            // Aksi Spesifik (Approve & Reject)
            Route::post('/{id}/approve', [ResepsionisPembatalanPesananController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ResepsionisPembatalanPesananController::class, 'reject'])->name('reject');
        });

        // Rute refund manual oleh resepsionis
        Route::post('/midtrans/refund/{id}', [ResepsionisMidtransController::class, 'refund'])->name('midtrans.refund');

        Route::controller(VerificationController::class)->prefix('verifikasi')->name('verifikasi.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::patch('/{id}/status', 'updateStatus')->name('updateStatus');
        });

        // Rute CRUD khusus User Pelanggan
        Route::resource('user', UserController::class);

        // Rute Manajemen Denda
        Route::get('/fine', [FineController::class, 'index'])->name('fine.index');
        Route::patch('/fine/{id}/status', [FineController::class, 'updateStatus'])->name('fine.updateStatus');

        // 6. Manajemen Pengembalian (CRUD)
        Route::resource('pengembalian', ResepsionisPengembalianController::class);

        //monitoring Transaksi Pembayaran
        Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
    });

Route::middleware(['auth', 'sopir'])
    ->prefix('sopir')
    ->name('sopir.')
    ->group(function () {

        // ==================== DASHBOARD & TUGAS ====================
        Route::get('/dashboard', [SopirDashboardController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/tugas-aktif', [SopirDashboardController::class, 'activeTasks'])
            ->name('activeTasks');

        Route::get('/riwayat', [SopirDashboardController::class, 'history'])
            ->name('history');
        // Rute Aksi (WRITE)
        Route::put('/status', [SopirActionController::class, 'updateStatus'])
            ->name('updateStatus');
        Route::post('/complete/{peminjaman}', [SopirActionController::class, 'completeTask'])
            ->name('completeTask');

        // ==================== LOGBOOK ====================
        Route::prefix('logbook')->name('logbook.')->group(function () {
            // Daftar tugas untuk logbook
            Route::get('/', [LogbookController::class, 'index'])
                ->name('index');

            // Form logbook untuk tugas tertentu
            Route::get('/{peminjaman}', [LogbookController::class, 'show'])
                ->name('show')
                ->where('peminjaman', '[0-9]+');

            // Simpan logbook
            Route::post('/{peminjaman}', [LogbookController::class, 'store'])
                ->name('store')
                ->where('peminjaman', '[0-9]+');
        });

        // ==================== AKTIVITAS SOPIR ====================
        Route::prefix('actions')->name('actions.')->group(function () {
            // Update status sopir
            Route::put('/status', [SopirActionController::class, 'updateStatus'])
                ->name('updateStatus');

            // Selesaikan tugas (alternatif dari logbook)
            Route::post('/complete/{peminjaman}', [SopirActionController::class, 'completeTask'])
                ->name('completeTask')
                ->where('peminjaman', '[0-9]+');

            // Update lokasi sopir (jika diperlukan)
            Route::post('/update-location', [SopirActionController::class, 'updateLocation'])
                ->name('updateLocation');
        });

        // ==================== PROFILE SOPIR ====================
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [SopirDashboardController::class, 'profile'])
                ->name('index');

            Route::put('/update', [SopirDashboardController::class, 'updateProfile'])
                ->name('update');
        });

        // ==================== NOTIFIKASI ====================
        Route::get('/notifications', [SopirDashboardController::class, 'notifications'])
            ->name('notifications');

        Route::post('/notifications/mark-read', [SopirActionController::class, 'markNotificationsAsRead'])
            ->name('notifications.markRead');
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Admin (Gunakan HomeIndex Class)
        Route::get('/dashboard', HomeIndex::class)->name('dashboard');

        // CRUD Mobil
        Route::get('/mobil', MobilIndex::class)->name('mobil');

        // Transaksi & Pembatalan
        Route::get('/transaksi', PeminjamanIndex::class)->name('peminjaman');

        // Manajemen User
        Route::get('/users', UserIndex::class)->name('users');

        // Manajemen Sopir
        Route::get('/sopir', SopirIndex::class)->name('sopir');

        // Verifikasi Identitas
        Route::get('/verifikasi', VerifikasiUserIndex::class)->name('verifikasi');

        // Manajemen Staff
        Route::get('/staff', StaffIndex::class)->name('staff');

        // Manajemen Resepsionis
        Route::get('/resepsionis', ResepsionisIndex::class)->name('resepsionis');

        // Manajemen Roles
        Route::get('/roles', RoleIndex::class)->name('roles');

        //manajemen pengembalian
        Route::get('/pengembalian', PengembalianIndex::class)->name('pengembalian');

        // Manajemen Pembatalan Pesanan
        Route::get('/pembatalan-pesanan', PembatalanPesananIndex::class)->name('pembatalan-pesanan');

        //View Vehicle Damage
        Route::get('/vehicle-damage', VehicleDamageIndex::class)->name('vehicle-damage');

        //view Vehicle Inspection
        Route::get('/vehicle-inspection', VehicleInspectionIndex::class)->name('vehicle-inspection');

        //view Driver Logbook
        Route::get('/driver-logbook', DriverLogbookIndex::class)->name('driver-logbook');
    });
