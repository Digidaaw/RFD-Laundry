<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
{{-- 
    PERBAIKAN:
    State untuk modal alert sekarang diinisialisasi langsung oleh PHP.
    Ini adalah cara yang lebih andal daripada menggunakan event listener JavaScript.
--}}
<body x-data="{
    showAlert: {{ session('success') || session('error') ? 'true' : 'false' }},
    
    @if(session('success'))
        alertType: 'success',
        alertTitle: 'Sukses',
        alertMessage: `{!! addslashes(session('success')) !!}`,
    @elseif(session('error'))
        alertType: 'error',
        alertTitle: 'Gagal',
        alertMessage: `{!! addslashes(session('error')) !!}`,
    @else
        alertType: '',
        alertTitle: '',
        alertMessage: '',
    @endif
}" class="font-sans">

    {{-- Di sinilah seluruh konten halaman akan ditampilkan --}}
    @yield('content')

    {{-- Panggil komponen modal notifikasi di luar konten utama --}}
    @include('components.modal.alert')

    {{-- Script lama tidak diperlukan lagi --}}

    @stack('scripts')
</body>
</html>
