<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    {{-- PERBAIKAN: Tambahkan 'defer' --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        /* ... Kustomisasi Choices.js ... */
        .choices__inner {
            background-color: white; border: 1px solid #d1d5db;
            border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 1rem;
        }
        .choices[data-type*="select-one"]::after { right: 1.5rem; }
    </style>
    
    @stack('styles')
</head>
<body x-data="{
    {{-- State untuk modal notifikasi --}}
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
    
    {{-- State untuk modal sign out --}}
    openSignOutModal: false
}"
{{-- Listener untuk event dari sidebar --}}
@open-signout-modal.window="openSignOutModal = true" 
class="font-sans bg-gray-100">

    @yield('content')

    {{-- Modal notifikasi global --}}
    @include('components.modal.alert')
    
    {{-- Modal sign out global --}}
    @include('components.modal.signout-modal')

    @stack('scripts')
</body>
</html>
