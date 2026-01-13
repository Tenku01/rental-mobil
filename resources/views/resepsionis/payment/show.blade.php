@extends('layouts.resepsionis')

@section('header', 'Monitoring Transaksi Pembayaran')

@section('content')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 border border-gray-200">
                
                <!-- Header Status -->
                <div class="flex justify-between items-start mb-8 border-b pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">ID Midtrans: {{ $transaction->midtrans_transaction_id }}</h3>
                        <p class="text-sm text-gray-500">Dibuat pada: {{ $transaction->created_at->format('d F Y, H:i:s') }}</p>
                    </div>
                    <div>
                        @php
                            $statusColor = match($transaction->status) {
                                'settlement', 'capture' => 'bg-green-100 text-green-800 border-green-200',
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'deny', 'cancel', 'expire' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <span class="px-4 py-2 rounded-full text-sm font-bold border {{ $statusColor }}">
                            {{ strtoupper($transaction->status) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Informasi Pembayaran -->
                    <div>
                        <h4 class="text-md font-semibold text-cyan-700 mb-3 border-b border-cyan-100 pb-1">Informasi Pembayaran</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Jumlah Bayar:</dt>
                                <dd class="font-bold text-gray-900 text-lg">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Tipe Transaksi:</dt>
                                <dd class="font-medium text-gray-900 uppercase">{{ $transaction->tipe_transaksi }}</dd>
                            </div>
                            @if($transaction->id_transaksi_awal)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Refund Dari ID:</dt>
                                <dd class="font-medium text-gray-900">#{{ $transaction->id_transaksi_awal }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Informasi Penyewa & Mobil -->
                    <div>
                        <h4 class="text-md font-semibold text-cyan-700 mb-3 border-b border-cyan-100 pb-1">Detail Peminjaman</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Nama Penyewa:</dt>
                                <dd class="font-medium text-gray-900">{{ $transaction->peminjaman->user->name ?? 'User Tidak Ditemukan' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Email:</dt>
                                <dd class="font-medium text-gray-900">{{ $transaction->peminjaman->user->email ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Mobil:</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $transaction->peminjaman->mobil->merek ?? '-' }} {{ $transaction->peminjaman->mobil->tipe ?? '' }}
                                    <span class="text-xs text-gray-500 block text-right">({{ $transaction->peminjaman->mobil_id ?? '-' }})</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Detail Teknis Midtrans (JSON) -->
                <div class="mt-8">
                    <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-1">Respon Teknis Midtrans</h4>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 overflow-x-auto">
                        <pre class="text-xs text-gray-600 font-mono">{{ json_encode(json_decode($transaction->midtrans_response), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>

                <!-- Tombol Kembali -->
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('resepsionis.transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Daftar
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection