@if ($paginator->hasPages())
    <div class="flex flex-col items-center justify-center mt-10 mb-10 space-y-3">

        {{-- 🔹 Tombol navigasi --}}
        <div class="flex items-center gap-3">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-gray-400 border rounded-md cursor-not-allowed">
                    PREVIOUS
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 bg-cyan-500 text-white rounded-md hover:bg-cyan-600 transition">
                    PREVIOUS
                </a>
            @endif

            {{-- Nomor Halaman --}}
            <div class="flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-2 text-gray-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-2 bg-cyan-500 text-white border border-cyan-600 rounded-md">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-cyan-100 transition">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 bg-cyan-500 text-white rounded-md hover:bg-cyan-600 transition">
                    NEXT
                </a>
            @else
                <span class="px-4 py-2 text-gray-400 border rounded-md cursor-not-allowed">
                    NEXT
                </span>
            @endif
        </div>
    </div>
@endif
