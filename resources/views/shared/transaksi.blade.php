@extends('layout.admin.layout')
@section('title', 'Kelola Transaksi')

@section('content')
<div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
    <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">
        @include('components.sidebar')
        <div class="ml-[347px]">
            <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                <h2 class="text-4xl font-semibold text-[#151d48]">Kelola Transaksi</h2>
                {{-- ... Topbar content ... --}}
            </header>

            {{-- 
                PERBAIKAN: Hapus 'openEditModal' dan 'editData' dari sini.
                Modal edit sekarang mandiri dan akan mendengarkan event.
            --}}
            <main class="p-10" x-data="{ 
                openAddModal: {{ $errors->any() ? 'true' : 'false' }},
                openDeleteModal: false,
                deleteUrl: ''
            }">
                <div class="flex justify-between items-center mb-8">
                    <button @click="openAddModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md">
                        + Tambah Transaksi
                    </button>
                    
                    <div class="flex items-center space-x-2 text-gray-700 font-bold text-lg">
                        <img src="{{ asset('assets/filter.svg') }}" alt="Filter Icon">
                        <span class="pl-2">Cari</span>
                        <div class="relative">
                            <form action="{{ route('transaksi.index') }}" method="GET">
                                <input type="text" name="search" placeholder="No. Invoice, Deskripsi, Nama..."
                                    value="{{ $search ?? '' }}"
                                    class="bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-80">
                                <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">No Invoice</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">Tanggal</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">Pelanggan</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">Deskripsi</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">Total Harga</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">Sisa Bayar</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700">Status</th>
                                    <th class="p-4 text-lg font-extrabold text-gray-700 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksis as $transaksi)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-semibold text-gray-800">{{ $transaksi->no_invoice }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d-m-Y') }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ $transaksi->pelanggan->name ?? 'N/A' }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ $transaksi->deskripsi ?? '-' }}</td>
                                    <td class="p-4 font-semibold text-gray-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4 font-semibold {{ $transaksi->sisa_bayar > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4">
                                        <span class="block px-3 py-1 text-sm rounded-full font-medium mb-1 w-fit" :class="{ 'bg-blue-100 text-blue-800': '{{ $transaksi->status_order }}' === 'Baru', 'bg-yellow-100 text-yellow-800': '{{ $transaksi->status_order }}' === 'Proses', 'bg-green-100 text-green-800': '{{ $transaksi->status_order }}' === 'Selesai' }">{{ $transaksi->status_order }}</span>
                                        <span class="block px-3 py-1 text-sm rounded-full font-medium w-fit" :class="{ 'bg-red-100 text-red-800': '{{ $transaksi->status_pembayaran }}' === 'Belum Lunas', 'bg-green-100 text-green-800': '{{ $transaksi->status_pembayaran }}' === 'Lunas' }">{{ $transaksi->status_pembayaran }}</span>
                                    </td>
                                    <td class="p-4 flex justify-center items-center space-x-2">
                                        @php
                                            $transaksiData = json_encode($transaksi);
                                        @endphp
                                        {{-- Tombol ini mengirim event yang akan didengar oleh modal edit --}}
                                        <button @click="$dispatch('open-edit-modal', {{ $transaksiData }})" class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200">Update</button>
                                        <button @click="openDeleteModal = true; deleteUrl = '{{ route('transaksi.destroy', $transaksi->id) }}';" class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200">Delete</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center p-6 text-gray-500">Tidak ada data transaksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Include semua modal --}}
                @include('components.modal.transaksi.add-transaksi')
                @include('components.modal.transaksi.edit-transaksi')
                @include('components.modal.transaksi.delete-transaksi')
            </main>
        </div>
    </div>
</div>
@endsection