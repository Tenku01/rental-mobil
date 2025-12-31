<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Verifikasi Identitas User</h2>

            @if (session()->has('message'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('message') }}</div>
            @endif
            @if (session()->has('warning'))
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('warning') }}</div>
            @endif

            <!-- Pilihan Filter -->
            <div class="mb-4">
                <select wire:model.live="filterStatus" class="border-gray-300 rounded text-sm">
                    <option value="menunggu">Menunggu Verifikasi</option>
                    <option value="disetujui">Sudah Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($identitas as $doc)
                <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition bg-gray-50">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-lg">{{ $doc->user->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $doc->user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">Diupload: {{ $doc->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded font-bold {{ $doc->status_approval == 'menunggu' ? 'bg-yellow-200 text-yellow-800' : ($doc->status_approval == 'disetujui' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800') }}">
                            {{ ucfirst($doc->status_approval) }}
                        </span>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <div class="w-1/2">
                            <p class="text-xs font-bold mb-1">KTP</p>
                            <a href="{{ asset('storage/'.$doc->ktp) }}" target="_blank">
                                <img src="{{ asset('storage/'.$doc->ktp) }}" class="w-full h-32 object-cover rounded border hover:opacity-75 cursor-pointer">
                            </a>
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs font-bold mb-1">SIM</p>
                             <a href="{{ asset('storage/'.$doc->sim) }}" target="_blank">
                                <img src="{{ asset('storage/'.$doc->sim) }}" class="w-full h-32 object-cover rounded border hover:opacity-75 cursor-pointer">
                            </a>
                        </div>
                    </div>

                    @if($doc->status_approval == 'menunggu')
                    <div class="mt-4 flex gap-2">
                        <button wire:confirm="Yakin setujui dokumen ini?" wire:click="approve({{ $doc->id }})" class="flex-1 bg-green-600 text-white py-2 rounded text-sm hover:bg-green-700">
                            Setujui
                        </button>
                        <button wire:confirm="Yakin tolak dokumen ini?" wire:click="reject({{ $doc->id }})" class="flex-1 bg-red-600 text-white py-2 rounded text-sm hover:bg-red-700">
                            Tolak
                        </button>
                    </div>
                    @endif
                </div>
                @empty
                <div class="col-span-2 text-center py-10 text-gray-500">
                    Tidak ada data identitas dengan status ini.
                </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $identitas->links() }}
            </div>

        </div>
    </div>
</div>