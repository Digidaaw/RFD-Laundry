@extends('layout.admin.layout')
@section('title', 'Pusat Laporan')

@section('content')
    <!-- Main Area -->
    <div>
        <!-- Topbar -->
        <header class="w-full h-[120px] sticky top-0 z-50 bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Tombol Hamburger untuk Mobile --}}
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Pusat Laporan</h2>
            </div>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <p class="uppercase font-semibold text-sm text-gray-900">{{ Auth::user()->role ?? 'Panel' }}</p>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 lg:p-10">
            <h3 class="text-2xl font-semibold text-gray-800 mb-6">Pilih Jenis Laporan</h3>

            {{-- Grid 2 kolom, akan menjadi 2 baris --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Opsi 1: Laporan Per Periode -->
                <a href="{{ route('report.periode') }}" class="block p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">Laporan Per Periode</h4>
                            <p class="text-gray-600">Lihat total pendapatan berdasarkan rentang tanggal.</p>
                        </div>
                    </div>
                </a>

                <!-- Opsi 2: Laporan Piutang -->
                <a href="{{ route('report.piutang') }}" class="block p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">Laporan Piutang</h4>
                            <p class="text-gray-600">Lihat semua transaksi yang belum lunas.</p>
                        </div>
                    </div>
                </a>
                
                <!-- Opsi 3: Laporan per Pelanggan -->
                <a href="{{ route('pelanggan.index') }}" class="block p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">Laporan per Pelanggan</h4>
                            <p class="text-gray-600">Cari pelanggan untuk melihat riwayat transaksinya.</p>
                        </div>
                    </div>
                </a>

            </div>
        </main>
    </div>
@endsection