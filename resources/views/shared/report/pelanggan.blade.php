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
                    <p class="uppercase font-semibold text-sm text-gray-900">{{ Auth::user()->role ?? 'Panel' }}</p>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 lg:p-10">

            <!-- Baris Atas: Kembali + Export -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-2 text-lg text-gray-600 hover:text-blue-600 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Pelanggan
                </a>

                <!-- Tombol Export (meneruskan filter tanggal yang aktif) -->
                <div class="flex gap-3">
                    <a href="{{ route('report.pelanggan.pdf', $pelanggan->id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                        target="_blank"
                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Export PDF
                    </a>
                    <a href="{{ route('report.pelanggan.excel', $pelanggan->id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}"
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25v1.5c0 .621.504 1.125 1.125 1.125m17.25-2.625v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <!-- Form Filter Rentang Tanggal -->
            <div class="bg-white rounded-xl shadow-md p-5 mb-8">
                <form method="GET" action="{{ route('report.pelanggan', $pelanggan->id) }}" class="flex flex-col md:flex-row items-stretch md:items-end gap-4 w-full">
                    <div class="w-full md:flex-1">
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                    </div>
                    <div class="w-full md:flex-1">
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                    </div>
                    <div class="w-full md:w-auto flex gap-2">
                        <button type="submit"
                            class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow text-sm font-semibold transition-colors">
                            Filter
                        </button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('report.pelanggan', $pelanggan->id) }}"
                                class="flex-1 md:flex-none text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg shadow text-sm font-semibold transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                @if($startDate && $endDate)
                    <p class="mt-3 text-sm text-blue-600 font-medium">
                        Menampilkan transaksi: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
                    </p>
                @else
                    <p class="mt-3 text-sm text-gray-400">Menampilkan semua riwayat transaksi</p>
                @endif
            </div>

            <!-- Ringkasan Totalan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Total Subtotal</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mt-2 whitespace-nowrap">Rp {{ number_format(max(0, $totalSubtotal), 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Total Potongan</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-red-600 mt-2 whitespace-nowrap">Rp {{ number_format(max(0, $totalPotongan ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Total Transaksi</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-blue-600 mt-2 whitespace-nowrap">Rp {{ number_format(max(0, $totalHarga), 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Total Sudah Dibayar</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-green-600 mt-2 whitespace-nowrap">Rp {{ number_format(max(0, $totalSudahBayar), 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Total Sisa Hutang</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-red-600 mt-2 whitespace-nowrap">Rp {{ number_format(max(0, $totalSisaHutang), 0, ',', '.') }}</p>
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
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700 w-40">Deskripsi</th>
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
                                                <div class="mb-1">{{ $item->layanan->name ?? 'N/A' }} ({{ $item->qty }} {{ $item->unit_satuan ?? $item->layanan->units->first()?->unit_satuan ?? 'pcs' }})</div>
                                            @endforeach
                                        @else
                                            {{ $transaksi->layanan->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-600">
                                        <span class="block max-w-[10rem] truncate" title="{{ $transaksi->deskripsi ?? '-' }}">
                                            {{ $transaksi->deskripsi ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-sm md:p-4 md:text-base font-semibold text-gray-900">Rp {{ number_format(max(0, $transaksi->total_harga), 0, ',', '.') }}</td>
                                    <td class="p-3 md:p-4">
                                        @if($transaksi->status_pembayaran === 'Lunas')
                                            <span class="px-3 py-1 text-xs md:text-sm font-semibold text-green-600">Lunas</span>
                                        @else
                                            <span class="px-3 py-1 text-xs md:text-sm font-semibold text-red-600">{{ $transaksi->status_pembayaran }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 md:p-4 align-middle">
                                        <div class="flex justify-center items-center space-x-1 md:space-x-2">
                                            @php
                                                $transaksiData = [
                                                    'id'                 => $transaksi->id,
                                                    'tanggal_order'      => $transaksi->tanggal_order,
                                                    'id_pelanggan'       => $transaksi->id_pelanggan,
                                                    'deskripsi'          => $transaksi->deskripsi,
                                                    'subtotal'           => $transaksi->subtotal,
                                                    'potongan'           => $transaksi->potongan,
                                                    'total_harga'        => $transaksi->total_harga,
                                                    'jumlah_bayar'       => $transaksi->jumlah_bayar,
                                                    'sisa_bayar'         => $transaksi->sisa_bayar,
                                                    'status_pembayaran'  => $transaksi->status_pembayaran,
                                                    'items'              => $transaksi->items->map(fn($item) => [
                                                        'id'           => $item->id,
                                                        'layanan_id'   => $item->layanan_id,
                                                        'unit_satuan'  => $item->unit_satuan,
                                                        'qty'          => $item->qty,
                                                        'harga_satuan' => $item->harga_satuan,
                                                        'subtotal'     => $item->subtotal,
                                                    ])->toArray(),
                                                ];
                                            @endphp

                                            <button @click="$dispatch('open-edit-modal', @js($transaksiData))"
                                                class="bg-yellow-100 text-yellow-800 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-yellow-200 transition text-sm md:text-base">
                                                Update
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center p-6 text-gray-500">
                                        @if($startDate && $endDate)
                                            Tidak ada transaksi pada rentang tanggal yang dipilih.
                                        @else
                                            Pelanggan ini belum memiliki riwayat transaksi.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Modal transaksi --}}
            @include('components.modal.transaksi.edit-transaksi')
            @include('components.modal.transaksi.delete-transaksi')
        </main>
    </div>
@endsection
