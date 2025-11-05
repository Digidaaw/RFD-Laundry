@extends('layout.admin.layout')
@section('title', 'Laporan Pelanggan')

@section('content')
<div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
    <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">
        @include('components.sidebar')
        <div class="ml-[347px]">
            <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                <div>
                    <h2 class="text-4xl font-semibold text-[#151d48]">Laporan Pelanggan</h2>
                    <p class="text-lg text-gray-500 mt-1">{{ $pelanggan->name }} - {{ $pelanggan->kontak }}</p>
                </div>
                {{-- ... Topbar content ... --}}
            </header>

            {{-- PERBAIKAN: Tambahkan x-data untuk modal hapus --}}
            <main class="p-10" x-data="{ openDeleteModal: false, deleteUrl: '' }">
                <div class="mb-8">
                    <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-2 text-lg text-gray-600 hover:text-blue-600 font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Daftar Pelanggan
                    </a>
                </div>
                
                {{-- ... (Ringkasan Totalan Akhir Anda) ... --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-lg font-semibold text-gray-500">Total Subtotal</h3>
                        <p class="text-3xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-lg font-semibold text-gray-500">Total Potongan</h3>
                        <p class="text-3xl font-bold text-red-500 mt-2">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-lg font-semibold text-gray-500">Total Transaksi</h3>
                        <p class="text-3xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-lg font-semibold text-gray-500">Total Sudah Dibayar</h3>
                        <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-lg font-semibold text-gray-500">Total Sisa Hutang</h3>
                        <p class="text-3xl font-bold text-red-600 mt-2">Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-2xl font-bold mb-4">Detail Riwayat Transaksi</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="p-4 font-extrabold text-gray-700">No Invoice</th>
                                    <th class="p-4 font-extrabold text-gray-700">Tanggal</th>
                                    <th class="p-4 font-extrabold text-gray-700">Layanan</th>
                                    <th class="p-4 font-extrabold text-gray-700">Deskripsi</th>
                                    <th class="p-4 font-extrabold text-gray-700">Total</th>
                                    <th class="p-4 font-extrabold text-gray-700">Status Bayar</th>
                                    <th class="p-4 font-extrabold text-gray-700 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksis as $transaksi)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-semibold text-gray-800">{{ $transaksi->no_invoice }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d M Y') }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ $transaksi->layanan->name ?? 'N/A' }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ $transaksi->deskripsi ?? '-' }}</td>
                                    <td class="p-4 font-semibold text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 text-sm rounded-full font-medium" 
                                              :class="{ 'bg-red-100 text-red-800': '{{ $transaksi->status_pembayaran }}' === 'Belum Lunas', 'bg-green-100 text-green-800': '{{ $transaksi->status_pembayaran }}' === 'Lunas' }">
                                            {{ $transaksi->status_pembayaran }}
                                        </span>
                                    </td>
                                    {{-- PERBAIKAN: Tambahkan tombol Update dan Delete --}}
                                    <td class="p-4 flex justify-center items-center space-x-2">
                                        @php
                                            $transaksiData = json_encode($transaksi);
                                        @endphp
                                        <button @click="$dispatch('open-edit-modal', {{ $transaksiData }})" class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200">Update</button>
                                        <button @click="openDeleteModal = true; deleteUrl = '{{ route('transaksi.destroy', $transaksi->id) }}';" class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200">Delete</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center p-6 text-gray-500">Pelanggan ini belum memiliki riwayat transaksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PERBAIKAN: Include modal yang diperlukan --}}
                @include('components.modal.transaksi.edit-transaksi')
                @include('components.modal.transaksi.delete-transaksi')
            </main>
        </div>
    </div>
</div>
@endsection