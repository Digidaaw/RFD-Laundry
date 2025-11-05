@extends('layout.admin.layout')
@section('title', 'Dashboard')

@section('content')
    <!-- Main Area -->
    <div>
        <!-- Topbar -->
        <header class="w-full h-[120px] bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Tombol Hamburger untuk Mobile --}}
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Sales Overview</h2>
            </div>

            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('img/rectangle-1393.png') }}"
                         class="w-[60px] h-[60px] rounded-full object-cover" alt="Profile">
                    <div class="hidden md:block">
                        {{-- Mengambil nama user yang sedang login --}}
                        <p class="text-base font-medium text-gray-900">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-sm text-gray-500">{{ Auth::user()->role ?? 'Panel' }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 md:p-10">

            <!-- Cards -->
            {{-- Dibuat responsif: 1 kolom di HP, 2 di tablet, 4 di desktop --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                
                <!-- Card Total User (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total User</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $totalUser ?? 0 }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Total akun terdaftar</p>
                </div>
                
                <!-- Card Total Order (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total Order</Selesai</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $totalOrder ?? 0 }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Transaksi sepanjang waktu</p>
                </div>
                
                <!-- Card Total Sales (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total Sales</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Total uang yang telah dibayar</p>
                </div>
                
                <!-- Card Order Pending (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Order Pending</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $orderPending ?? 0 }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Status 'Baru' atau 'Proses'</p>
                </div>
            </div>

            <!-- Chart placeholder (Seperti yang Anda minta, ini dibiarkan) -->
            <div class="mt-12 w-full h-[400px] bg-white rounded-xl shadow-md flex items-center justify-center text-gray-400 text-lg">
                [ Chart Component Placeholder ]
            </div>

        </main>
    </div>
@endsection