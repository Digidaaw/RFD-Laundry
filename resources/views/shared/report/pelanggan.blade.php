@extends('layout.admin.layout')
@section('title', 'Laporan Pelanggan')

@section('content')
    <!-- Main Area -->
    <div>
        <!-- Topbar -->
        <header class="w-full h-[120px] bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Laporan Pelanggan</h2>
                    <p class="text-base lg:text-lg text-gray-500 mt-1">{{ $pelanggan->name }} - {{ $pelanggan->kontak }}</p>
                </div>
            </div>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('img/rectangle-1393.png') }}" class="w-[60px] h-[60px] rounded-full object-cover" alt="Profile">
                    <div class="hidden lg:block">
                        <p class="text-base font-medium text-gray-900">Admin</p>
                        <p class="text-sm text-gray-500">Panel</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 lg:p-10">

            <!-- Tombol Kembali -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <!-- Tombol Kembali -->
                <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-2 text-lg text-gray-600 hover:text-blue-600 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Pelanggan
                </a>

                <!-- Tombol Export -->
                <div class="flex gap-3">
                    <a href="{{ route('report.pelanggan.pdf', $pelanggan->id) }}" target="_blank" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Export PDF
                    </a>
                    <a href="{{ route('report.pelanggan.excel', $pelanggan->id) }}" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors"> 
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25v1.5c0 .621.504 1.125 1.125 1.125m17.25-2.625v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125" /> 
                </svg> Export Excel </a>       
            </div>
            </div>
            
            <!-- Ringkasan Totalan Akhir -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Subtotal</h3>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Potongan</h3>
                    <p class="text-2xl lg:text-3xl font-bold text-red-500 mt-2">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Transaksi</h3>
                    <p class="text-2xl lg:text-3xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Sudah Dibayar</h3>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Total Sisa Hutang</h3>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600 mt-2">Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Tabel Detail Riwayat Transaksi -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">No Invoice</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Tanggal</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Layanan</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Deskripsi</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Total</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Status Bayar</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-800">{{ $transaksi->no_invoice }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-600">{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d M Y') }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-600">
                                        @if($transaksi->items->count() > 0)
                                            @foreach($transaksi->items as $item)
                                                <div class="mb-1">{{ $item->layanan->name ?? 'N/A' }} ({{ $item->qty }} {{ $item->layanan->units->first()?->unit_satuan ?? 'pcs' }})</div>
                                            @endforeach
                                        @else
                                            {{ $transaksi->layanan->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-600">{{ $transaksi->deskripsi ?? '-' }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="p-3 md:p-4">
                                        <span class="px-3 py-1 text-xs md:text-sm rounded-full font-medium" 
                                              :class="{ 'bg-red-100 text-red-800': '{{ $transaksi->status_pembayaran }}' === 'Belum Lunas', 'bg-green-100 text-green-800': '{{ $transaksi->status_pembayaran }}' === 'Lunas' }">
                                            {{ $transaksi->status_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="p-3 md:p-4 flex justify-center items-center space-x-1 md:space-x-2">
                                        @php 
                                            // Data transaksi yang akan dikirim ke modal
                                            $transaksiData = json_encode($transaksi); 
                                        @endphp
                                        
                                        {{-- PERBAIKAN: Menggunakan $transaksiData (BUKAN $pelangganData) --}}
                                        <button @click="$dispatch('open-edit-modal', {{ $transaksiData }})" 
                                            class="bg-green-100 text-green-700 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-green-200 text-sm md:text-base transition">
                                            Update
                                        </button>
                                        
                                        <button @click="openDeleteModal = true; deleteUrl = '{{ route('transaksi.destroy', $transaksi->id) }}';" 
                                            class="bg-red-100 text-red-700 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-red-200 text-sm md:text-base transition">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center p-6 text-gray-500">Pelanggan ini belum memiliki riwayat transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Menggunakan modal transaksi (karena ini adalah list transaksi) --}}
            @include('components.modal.transaksi.edit-transaksi')
            @include('components.modal.transaksi.delete-transaksi')
        </main>
    </div>
@endsection