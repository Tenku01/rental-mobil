@extends('layouts.staff')

@section('content')

{{--
PERHATIAN: Data $metrics dan $latestChecks diambil langsung dari StaffDashboardController.
Tidak ada logika data atau simulasi di sini.
--}}

<div class="space-y-8">

    <div class="flex items-center justify-between pb-2 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-gray-900">Selamat Datang, Staff!</h1>
        {{-- Menggunakan Carbon untuk menampilkan tanggal --}}
        <p class="text-base font-medium text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Kartu Metrik Dinamis (2 Kartu sesuai controller) --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
        @foreach ($metrics as $metric)
            <div class="bg-white overflow-hidden shadow-lg rounded-xl transition duration-300 hover:shadow-xl p-6 border-b-4 border-{{ $metric['color'] === 'yellow' ? 'yellow' : 'green' }}-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-{{ $metric['color'] === 'yellow' ? 'yellow' : 'green' }}-600 rounded-xl p-3 shadow-md">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            {!! $metric['icon_path'] !!}
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">{{ $metric['label'] }}</dt>
                            <dd class="flex items-baseline">
                                <div class="text-3xl font-extrabold text-gray-900">
                                    {{ number_format($metric['value'], 0, ',', '.') }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tabel Pengecekan Terakhir Dinamis --}}
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">5 Pengecekan Terakhir & Menunggu</h2>
        <div class="bg-white shadow-xl overflow-hidden rounded-xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu Kembali</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penyewa / Mobil</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($latestChecks as $check)
                            @php
                                $statusClass = match(strtolower($check->status)) {
                                    'selesai' => 'bg-green-100 text-green-800',
                                    'selesai pengecekan' => 'bg-blue-100 text-blue-800', 
                                    'menunggu pengecekan' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $statusLabel = match(strtolower($check->status)) {
                                    'selesai' => 'Selesai',
                                    'selesai pengecekan' => 'Selesai Cek',
                                    'menunggu pengecekan' => 'Perlu Dicek',
                                    default => ucfirst($check->status)
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ $check->kode_pengembalian }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($check->tanggal_pengembalian)->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-medium">{{ $check->peminjaman->user->name ?? 'Pengguna Tidak Dikenal' }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $check->peminjaman->mobil->merek ?? '-' }} 
                                        ({{ $check->peminjaman->mobil_id ?? 'N/A' }})
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if (strtolower($check->status) === 'menunggu pengecekan')
                                        <a href="{{ route('staff.pengecekan.view', $check->kode_pengembalian) }}" class="text-red-600 hover:text-red-900 font-bold border border-red-200 bg-red-50 px-3 py-1 rounded hover:bg-red-100">
                                            PROSES
                                        </a>
                                    @else
                                        <a href="{{ route('staff.pengecekan.detail', $check->kode_pengembalian) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 italic">
                                    Tidak ada data pengecekan yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Akses Cepat --}}
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Akses Cepat</h2>
        <a href="{{ route('staff.pengecekan.index') }}"
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-md
                  text-white bg-cyan-600 hover:bg-cyan-700
                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500
                  transition duration-150 transform hover:scale-[1.02]">
            <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Cari Pengembalian
        </a>
    </div>

</div>
@endsection