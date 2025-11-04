@extends('layout.admin.layout')
@section('title', 'Pusat Laporan')

@section('content')
<div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
    <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">
        @include('components.sidebar')
        <div class="ml-[347px]">
            <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                <h2 class="text-4xl font-semibold text-[#151d48]">Pusat Laporan</h2>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('img/rectangle-1393.png') }}"
                            class="w-[60px] h-[60px] rounded-full object-cover" alt="Profile">
                        <div>
                            <p class="text-base font-medium text-gray-900">Admin</p>
                            <p class="text-sm text-gray-500">Panel</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Card Laporan Per Periode -->
                    <a href="{{ route('report.periode') }}" class="block bg-white p-8 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="bg-blue-100 p-4 rounded-full">
                                <svg class="w-10 h-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h22.5" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">Laporan Per Periode</h3>
                                <p class="text-gray-500 mt-1">Lihat pendapatan dan transaksi dalam rentang waktu tertentu.</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card Laporan Piutang -->
                    <a href="{{ route('report.piutang') }}" class="block bg-white p-8 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="bg-red-100 p-4 rounded-full">
                                <svg class="w-10 h-10 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">Laporan Piutang</h3>
                                <p class="text-gray-500 mt-1">Lihat daftar pelanggan yang memiliki tunggakan pembayaran.</p>
                            </div>
                        </div>
                    </a>

                </div>
            </main>
        </div>
    </div>
</div>
@endsection
