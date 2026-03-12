@extends('layout.admin.layout')
@section('title', 'Laporan Per Periode')

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
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Laporan Per Periode</h2>
            </div>
            <div class="flex items-center gap-8">
                {{-- ... (Profil Admin) ... --}}
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 lg:p-10">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="{{ route('report.index') }}" class="flex items-center gap-2 text-lg text-gray-600 hover:text-blue-600 font-semibold w-fit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Pusat Laporan
                </a>
            </div>

            <!-- Filter Form -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <form action="{{ route('report.periode') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="start_date" class="block font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label for="end_date" class="block font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md h-11">Tampilkan Laporan</button>
                    </div>
                </form>
            </div>

            <!-- Ringkasan Data -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Potensi Pendapatan</h3>
                    <p class="text-3xl font-bold text-blue-600 mt-2">Rp {{ number_format($potensiPendapatan, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Pendapatan Diterima</h3>
                    <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($pendapatanLunas, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Jumlah Transaksi</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTransaksi }}</p>
                </div>
            </div>

            <!-- Tabel Hasil + Export -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                    <h3 class="text-2xl font-bold">Detail Transaksi</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('report.periode', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'export' => 'pdf']) }}"
                           class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm shadow">
                            <span>Export PDF</span>
                        </a>
                        <a href="{{ route('report.periode', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'export' => 'excel']) }}"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm shadow">
                            <span>Export Excel</span>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">No Invoice</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Tanggal</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Pelanggan</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Kasir</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Total</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Status Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->no_invoice }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d M Y') }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->pelanggan->name ?? 'N/A' }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->user->name ?? 'N/A' }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                <td class="p-3 md:p-4">
                                    <span class="px-3 py-1 text-xs md:text-sm rounded-full font-medium ..." 
                                          :class="{ 'bg-red-100 text-red-800': '{{ $transaksi->status_pembayaran }}' === 'Belum Lunas', 'bg-green-100 text-green-800': '{{ $transaksi->status_pembayaran }}' === 'Lunas' }">
                                        {{ $transaksi->status_pembayaran }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center p-6 text-gray-500">Tidak ada data transaksi pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
@endsection