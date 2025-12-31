<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config('app.name', 'RentCar Sopir') }}</title>

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
    
    {{-- Font Inter untuk estetika --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">

{{-- Struktur Div utama sesuai contoh user, inisialisasi Alpine data --}}
<div x-data="{ sidebarOpen: true, userMenuOpen: false }" class="min-h-screen flex">

    {{-- SIDEBAR --}}
    @include('components.sidebar-sopir')

    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- HEADER --}}
        @include('components.headbar-sopir')

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 sm:p-6">
            @yield('content')
        </main>

    </div>
</div>

{{-- ALPINE JS CDN sesuai contoh user --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>