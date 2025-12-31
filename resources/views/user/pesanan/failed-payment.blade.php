@extends('layouts.app')

@section('content')
<div class="text-center mt-5">
    <h2>❌ Pembayaran Gagal atau Dibatalkan</h2>
    <p>Silakan coba lagi atau hubungi admin jika ada kendala.</p>
    <a href="{{ route('mobils.index') }}" class="btn btn-secondary mt-3">Kembali ke Mobil</a>
</div>
@endsection
