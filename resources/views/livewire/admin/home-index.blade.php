<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <button wire:click="openExportModal" class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md flex items-center transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Download Laporan
            </button>
        </div>

        <!-- KARTU STATISTIK (Sama) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 p-3 rounded-full"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Total Pendapatan</p><p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Armada Tersedia</p><p class="text-lg font-bold text-gray-800">{{ $mobilTersedia }} <span class="text-xs font-normal text-gray-400">/ {{ $totalMobil }} Unit</span></p></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-full"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Sedang Disewa</p><p class="text-lg font-bold text-gray-800">{{ $mobilDisewa }} Unit</p></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full"><svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Butuh Verifikasi</p><p class="text-lg font-bold text-gray-800">{{ $pendingVerifikasi }} User</p></div>
                </div>
            </div>
        </div>

        <!-- GRAFIK 1: PENDAPATAN (Full Width) -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Grafik Pendapatan Tahun Ini</h3>
            <div id="revenueChart"></div>
        </div>

        <!-- ROW 2: DENDA & MOBIL TERLARIS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- GRAFIK 2: DENDA (Bar Chart) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Statistik Denda Terkumpul</h3>
                <div id="fineChart"></div>
            </div>

            <!-- GRAFIK 3: MOBIL TERLARIS (Donut Chart) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Top 5 Mobil Paling Laris</h3>
                <div id="topCarChart" class="flex justify-center"></div>
            </div>
        </div>

        <!-- TRANSAKSI TERBARU -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-700">Transaksi Terbaru</h3>
                <a href="{{ route('admin.peminjaman') }}" class="text-xs text-blue-500 hover:underline">Lihat Semua</a>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recentTransactions as $trx)
                <li class="py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $trx->user->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500">{{ $trx->mobil->merek ?? '-' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $trx->status == 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </div>
                </li>
                @empty
                <li class="py-3 text-center text-gray-500 text-xs">Belum ada transaksi.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- MODAL FILTER EXPORT (Sama seperti sebelumnya) -->
    @if($showExportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" wire:click="$set('showExportModal', false)"></div>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100">
                <div class="bg-white px-6 py-6">
                    <h3 class="text-lg leading-6 font-bold text-gray-800 mb-4">Filter Laporan Transaksi</h3>
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-gray-700">Tanggal Awal</label><input wire:model="dateStart" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">@error('dateStart') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                        <div><label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label><input wire:model="dateEnd" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">@error('dateEnd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror</div>
                        <div><label class="block text-sm font-medium text-gray-700">Status</label><select wire:model="filterStatusExport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">Semua Status</option><option value="selesai">Selesai</option><option value="berlangsung">Berlangsung</option><option value="sudah dibayar lunas">Sudah Dibayar</option><option value="dibatalkan">Dibatalkan</option></select></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2">
                    <button wire:click="downloadReport" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Unduh PDF</button>
                    <button wire:click="$set('showExportModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SCRIPTS GRAFIK (APEXCHARTS) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // 1. CHART PENDAPATAN (Area)
            var optionsRevenue = {
                series: [{ name: 'Pendapatan', data: @json($chartData) }],
                chart: { type: 'area', height: 300, toolbar: { show: false } },
                colors: ['#10B981'], // Green
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth' },
                xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'] },
                tooltip: { y: { formatter: function (val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val) } } }
            };
            var chartRev = new ApexCharts(document.querySelector("#revenueChart"), optionsRevenue);
            chartRev.render();

            // 2. CHART DENDA (Bar)
            var optionsFine = {
                series: [{ name: 'Denda', data: @json($fineData) }],
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                colors: ['#EF4444'], // Red
                plotOptions: { bar: { borderRadius: 4, horizontal: false } },
                dataLabels: { enabled: false },
                xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'] },
                tooltip: { y: { formatter: function (val) { return "Rp " + new Intl.NumberFormat('id-ID').format(val) } } }
            };
            var chartFine = new ApexCharts(document.querySelector("#fineChart"), optionsFine);
            chartFine.render();

            // 3. CHART MOBIL TERLARIS (Donut)
            var optionsTop = {
                series: @json($topMobilData), // Array Angka
                labels: @json($topMobilLabels), // Array String Nama Mobil
                chart: { type: 'donut', height: 320 },
                colors: ['#3B82F6', '#6366F1', '#8B5CF6', '#EC4899', '#F59E0B'],
                legend: { position: 'bottom' },
                responsive: [{ breakpoint: 480, options: { chart: { width: 300 }, legend: { position: 'bottom' } } }]
            };
            
            // Render hanya jika ada data
            if (@json($topMobilData).length > 0) {
                var chartTop = new ApexCharts(document.querySelector("#topCarChart"), optionsTop);
                chartTop.render();
            } else {
                document.querySelector("#topCarChart").innerHTML = "<p class='text-center text-gray-500 py-10'>Belum ada data penyewaan.</p>";
            }
        });
    </script>
</div>