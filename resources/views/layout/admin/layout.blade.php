<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Aset untuk Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Kustomisasi Choices.js */
        .choices__inner {
            background-color: white;
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.5rem; /* rounded-lg */
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
        .choices[data-type*="select-one"]::after {
            right: 1.5rem;
        }
    </style>
    
    @stack('styles')
</head>
{{-- 
    PERBAIKAN: Mengembalikan inisialisasi state untuk modal alert.
    Ini akan membaca session dari Laravel dan menyiapkan data untuk ditampilkan.
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

    @yield('content')

    @include('components.modal.alert')

    @stack('scripts')
</body>
</html>
