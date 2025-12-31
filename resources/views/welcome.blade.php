

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Aka Rental</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

         @php
    $isProduction = app()->environment('production');
    $manifestPath = $isProduction ? '../public_html/build/manifest.json' : public_path('build/manifest.json');
@endphp

@if ($isProduction && file_exists($manifestPath))
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true);
    @endphp
    <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
@else
    @viteReactRefresh
    @vite(['resources/js/app.js', 'resources/css/app.css'])
@endif
        </head>
    <body class="antialiased relative bg-cover bg-center bg-no-repeat" 
      style="background-image: url('{{ asset('images/bg3.jpg') }}');">
   <div class="bg-gray-0">
 <!-- resources/views/components/navbar.blade.php -->

<nav x-data="{ open: false }" class="bg-white shadow-md fixed w-full top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
        <!-- 🔹 Logo di pojok kiri -->
        <a href="#beranda" class="flex items-center space-x-2">
            <img src="/logoakarentcar.png" alt="Aka Rent Car" class="h-12 w-auto object-contain" />
            <span class="sr-only">Aka Rent Car</span>
        </a>

        <!-- 🔹 Menu untuk layar besar -->
        <ul class="hidden md:flex space-x-8 text-sm font-semibold text-gray-800">
            <li><a href="#beranda" class="hover:text-cyan-600">Beranda</a></li>
            <li><a href="#armada" class="hover:text-cyan-600">Armada</a></li>
            <li><a href="#tentang" class="hover:text-cyan-600">Tentang Kami</a></li>
            <li><a href="#faq" class="hover:text-cyan-600">FAQ</a></li>
            <li><a href="#kontak" class="hover:text-cyan-600">Kontak</a></li>
            <li>
                <a href="{{ route('login') }}"
                    class="text-cyan-600 border border-cyan-600 px-3 py-1 rounded-lg hover:bg-cyan-600 hover:text-white transition">
                       Login
                    </a>
            </li>
        </ul>

        <!-- 🔹 Tombol Hamburger -->
        <button @click="open = !open" class="md:hidden text-cyan-600 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- 🔹 Menu versi mobile -->
    <div x-show="open" @click.away="open = false" class="md:hidden bg-white border-t border-gray-200">
        <ul class="flex flex-col text-center py-4 space-y-2 font-medium">
            <li><a href="#beranda" class="block py-2 hover:text-cyan-600">Beranda</a></li>
            <li><a href="#armada" class="block py-2 hover:text-cyan-600">Armada</a></li>
            <li><a href="#tentang" class="block py-2 hover:text-cyan-600">Tentang Kami</a></li>
            <li><a href="#faq" class="block py-2 hover:text-cyan-600">FAQ</a></li>
            <li><a href="#kontak" class="block py-2 hover:text-cyan-600">Kontak</a></li>
            @auth
                <li><a href="{{ url('/dashboard') }}" class="block py-2 hover:text-cyan-600">Dashboard</a></li>
            @else
                <li>
                    <a href="{{ route('login') }}"
                       class="block mx-20 py-2 border border-cyan-600 text-cyan-600 rounded-lg hover:bg-cyan-600 hover:text-white transition">
                       Login
                    </a>
                </li>
            @endauth
        </ul>
    </div>
</nav>


  <div class="relative isolate px-6 pt-14 lg:px-8">
    <div aria-hidden="true" class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
      <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"></div>
    </div>
    <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
      <div class="hidden sm:mb-8 sm:flex sm:justify-center">
   
      </div>
      <div class="text-center">
        <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Sewa Mobil Tanpa Ribet.</h1>
        <p class="mt-8 text-lg font-medium text-pretty text-gray-900 sm:text-xl/8">Rental mobil cepat, aman, dan nyaman untuk semua kebutuhan Anda.
Booking mudah, mobil siap pakai kapan saja.</p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
<a href="{{ route('login') }}" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-cyan-500 border-2 border-cyan-500 shadow-xs hover:bg-cyan-500 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-500">
  PESAN SEKARANG
</a>
        </div>
      </div>
    </div>
    <div aria-hidden="true" class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]">
      <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"></div>
    </div>
  </div>
 <!-- 🔹 SECTION 2: DAFTAR MOBIL -->
    <section id="daftar-mobil" class="max-w-7xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center mb-8">Daftar Mobil Kami</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @foreach ($mobils as $mobil)
                <div 
                    class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300 cursor-pointer"
                    @auth
                        onclick="window.location='{{ url('/mobil/' . $mobil->id) }}'"
                    @else
                        onclick="window.location='{{ route('login') }}'"
                    @endauth
                >
                    <div class="relative">
                        <img 
                            src="{{ asset('storage/' . $mobil->foto) }}" 
                            alt="{{ $mobil->tipe }}" 
                            class="w-full h-52 object-cover rounded-lg"
                        >
                        <div class="absolute bottom-0 left-0 bg-black bg-opacity-60 text-white text-sm font-semibold px-3 py-1 rounded-tr-lg">
                            Rp {{ number_format($mobil->harga, 0, ',', '.') }} / hari
                        </div>
                    </div>

                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $mobil->tipe }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ $mobil->merek }}</p>

                        <div class="flex flex-col gap-1 text-sm text-gray-600">
                            <span>Warna: {{ $mobil->warna }}</span>
                            <span>Transmisi: {{ $mobil->transmisi }}</span>
                            <span>Kursi: {{ $mobil->kursi }}</span>
                        </div>

                        <div class="mt-4">
                            @auth
                                <a href="{{ url('/mobil/' . $mobil->id) }}" 
                                   class="block text-center bg-cyan-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-cyan-600 transition">
                                   Pesan Sekarang
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="block text-center bg-cyan-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-cyan-600 transition">
                                   Pesan Sekarang
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 🔹 SECTION 3: TENTANG -->
    <section id="tentang" class="bg-white py-16">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">Tentang Kami</h2>
            <p class="text-gray-600 leading-relaxed">
                Kami menyediakan layanan rental mobil dengan berbagai pilihan tipe dan harga kompetitif.
                Kepuasan pelanggan adalah prioritas utama kami.
            </p>
        </div>
    </section>

    <!-- 🔹 SECTION 4: KONTAK -->
    <section id="kontak" class="bg-gray-900 text-white py-16">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">Hubungi Kami</h2>
            <p>Email: Akarent@gmail.com</p>
            <p>Telepon: 0812-3456-7890</p>
            <p class="mt-6 text-gray-400 text-sm">&copy; {{ date('Y') }} CobaRentalMobil. Semua Hak Dilindungi.</p>
        </div>
    </section>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>