<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        .choices__inner {
            background-color: white; border: 1px solid #d1d5db;
            border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 1rem;
        }
        .choices[data-type*="select-one"]::after { right: 1.5rem; }
    </style>
    
    @stack('styles')
</head>
<body x-data="{
    // State Global
    sidebarOpen: false,
    openSignOutModal: false,
    showAlert: {{ session('success') || session('error') ? 'true' : 'false' }},
    alertType: '{{ session('success') ? 'success' : (session('error') ? 'error' : '') }}',
    alertTitle: '{{ session('success') ? 'Sukses' : (session('error') ? 'Gagal' : '') }}',
    alertMessage: `{!! session('success') ? addslashes(session('success')) : (session('error') ? addslashes(session('error')) : '') !!}`,

    // State Modal CRUD (untuk semua halaman)
    openAddModal: {{ $errors->any() ? 'true' : 'false' }},
    openDeleteModal: false,
    deleteUrl: '',
    openEditModal: false,
    editData: {},

    // =================================================================
    // PERBAIKAN: Logika Kalkulasi Transaksi dipindah ke sini
    // =================================================================
    selectedLayanan: null, 
    berat: 0, 
    potongan: 0,
    bayar: 0,
    subtotal: 0,
    total: 0,
    sisa: 0,
    layanans: [], // Akan diisi oleh halaman transaksi

    updateSubtotal() {
        const layanan = this.layanans.find(l => l.id == this.selectedLayanan);
        if (layanan && this.berat > 0) {
            this.subtotal = layanan.harga * this.berat;
        } else {
            this.subtotal = 0;
        }
        this.updateTotal(); 
    },
    updateTotal() {
        this.total = this.subtotal - this.potongan;
        if (this.total < 0) {
            this.total = 0;
        }
        this.updateSisa(); 
    },
    updateSisa() {
        this.sisa = this.total - this.bayar;
        if (this.sisa < 0) {
            this.sisa = 0;
        }
    },
    
    // Fungsi untuk mereset kalkulator saat modal ditutup
    resetCalculator() {
        this.selectedLayanan = null;
        this.berat = 0;
        this.potongan = 0;
        this.bayar = 0;
        this.subtotal = 0;
        this.total = 0;
        this.sisa = 0;
    }
    // =================================================================
}"
@open-signout-modal.window="openSignOutModal = true" 
class="font-sans bg-gray-100">

    <div class="relative min-h-screen lg:flex">
        
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Konten Utama -->
        <div class="flex-1">
            @yield('content')
        </div>

        <!-- Overlay gelap saat sidebar mobile terbuka -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
        </div>
    </div>

    @include('components.modal.alert')
    @include('components.modal.signout-modal')

    @stack('scripts')
</body>
</html>