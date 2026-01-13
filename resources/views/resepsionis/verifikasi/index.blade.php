@extends('layouts.resepsionis')

@section('header', 'Verifikasi Identitas Pelanggan')

@section('content')

    <div class="container mx-auto">
        <!-- Card Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Antrean Verifikasi</h3>
                    <p class="text-sm text-gray-500">Tinjau dokumen KTP dan SIM pelanggan sebelum memberikan izin sewa.</p>
                </div>
                <div class="flex items-center space-x-2 bg-blue-100 px-3 py-1 rounded-full text-blue-700 text-xs font-bold">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    <span>{{ $identities->where('status_approval', 'menunggu')->count() }} Menunggu</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Waktu Unggah</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse($identities as $idnt)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-9 w-9 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold">
                                            {{ strtoupper(substr($idnt->user_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-bold text-gray-900">{{ $idnt->user_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $idnt->user_email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($idnt->tanggal_upload)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($idnt->status_approval == 'menunggu')
                                        <span
                                            class="px-3 py-1 text-[11px] font-bold bg-amber-100 text-amber-700 rounded-md uppercase">Pending</span>
                                    @elseif($idnt->status_approval == 'disetujui')
                                        <span
                                            class="px-3 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-700 rounded-md uppercase">Approved</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-[11px] font-bold bg-rose-100 text-rose-700 rounded-md uppercase">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('resepsionis.verifikasi.show', $idnt->id) }}"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="italic text-sm">Belum ada dokumen yang perlu diverifikasi.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


    </div>
@endsection
