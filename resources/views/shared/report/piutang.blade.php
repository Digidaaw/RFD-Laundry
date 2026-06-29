@extends('layout.admin.layout')
@section('title', 'Laporan Piutang')

@section('content')
    <div>
        <header class="w-full h-[120px] bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Laporan Piutang</h2>
            </div>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <p class="uppercase font-semibold text-sm text-gray-900">{{ Auth::user()->role ?? 'Panel' }}</p>
                </div>
            </div>
        </header>

        <main class="p-6 lg:p-10">

            <div class="mb-6">
                <a href="{{ route('report.index') }}" class="flex items-center gap-2 text-lg text-gray-600 hover:text-blue-600 font-semibold w-fit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Pusat Laporan
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <form action="{{ route('report.piutang') }}" method="GET">
                    <div class="flex items-center space-x-2 text-gray-700 font-bold text-lg w-full">
                        <span class="pl-2">Cari Pelanggan</span>
                        <div class="relative w-full md:w-1/2">
                            <input type="text" name="search" placeholder="Nama atau Kontak..." value="{{ request('search') }}"
                                   class="bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                            <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md">Cari</button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Total Piutang</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-red-600 mt-2 whitespace-nowrap">Rp {{ number_format(max(0, $totalPiutang), 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md flex flex-col justify-between h-full">
                    <h3 class="text-lg font-semibold text-gray-500">Jumlah Transaksi Belum Lunas</h3>
                    <p class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 mt-2 whitespace-nowrap">{{ $piutangs->count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <h3 class="text-2xl font-bold mb-4">Detail Piutang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">No Invoice</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Tanggal</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Pelanggan</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700">Sisa Bayar</th>
                                <th class="p-3 text-sm md:p-4 md:text-base font-extrabold text-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($piutangs as $transaksi)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-sm md:p-4 md:text-base text-gray-600">{{ $transaksi->no_invoice }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base text-gray-600">{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d M Y') }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base text-gray-600">{{ $transaksi->pelanggan->name ?? 'N/A' }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base font-bold text-red-600">Rp {{ number_format(max(0, $transaksi->sisa_bayar), 0, ',', '.') }}</td>
                                <td class="p-3 md:p-4 text-center">
                                    @php
                                    $transaksiData = [
                                        'id' => $transaksi->id,
                                        'tanggal_order' => $transaksi->tanggal_order,
                                        'id_pelanggan' => $transaksi->id_pelanggan,
                                        'deskripsi' => $transaksi->deskripsi,
                                        'subtotal' => $transaksi->subtotal,
                                        'potongan' => $transaksi->potongan,
                                        'total_harga' => $transaksi->total_harga,
                                        'jumlah_bayar' => $transaksi->jumlah_bayar,
                                        'sisa_bayar' => $transaksi->sisa_bayar,
                                        'status_pembayaran' => $transaksi->status_pembayaran,
                                        'items' => $transaksi->items->map(fn($item) => [
                                            'id' => $item->id,
                                            'layanan_id' => $item->layanan_id,
                                            'unit_satuan' => $item->unit_satuan,
                                            'qty' => $item->qty,
                                            'harga_satuan' => $item->harga_satuan,
                                            'subtotal' => $item->subtotal,
                                        ])->toArray(),
                                    ];
                                    @endphp
                                    <button @click="$dispatch('open-edit-modal', @js($transaksiData))"
                                        class="bg-yellow-100 text-yellow-800 font-bold py-2 px-6 rounded-md hover:bg-yellow-200">
                                        Bayar
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center p-6 text-gray-500">Tidak ada data piutang.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @include('components.modal.transaksi.edit-transaksi')
        </main>
    </div>
@endsection
