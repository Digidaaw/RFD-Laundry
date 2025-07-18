@extends('layout.admin.layout')
@section('title', 'Laporan Per Periode')

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">
            @include('components.sidebar')
            <div class="ml-[347px]">
                <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                    <h2 class="text-4xl font-semibold text-[#151d48]">Laporan Per Periode</h2>
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
                    <!-- Filter Form -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <form action="{{ route('report.periode') }}" method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label for="start_date" class="block font-semibold text-gray-700 mb-2">Tanggal
                                        Mulai</label>
                                    <input type="date" id="start_date" name="start_date"
                                        value="{{ $startDate->format('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                </div>
                                <div>
                                    <label for="end_date" class="block font-semibold text-gray-700 mb-2">Tanggal
                                        Selesai</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                </div>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md h-11">Tampilkan
                                    Laporan</button>
                            </div>
                        </form>
                    </div>

                    <!-- Ringkasan Data -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-lg font-semibold text-gray-500">Potensi Pendapatan</h3>
                            <p class="text-3xl font-bold text-gray-800 mt-2">Rp
                                {{ number_format($potensiPendapatan, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-lg font-semibold text-gray-500">Pendapatan Diterima</h3>
                            <p class="text-3xl font-bold text-green-600 mt-2">Rp
                                {{ number_format($pendapatanLunas, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-lg font-semibold text-gray-500">Jumlah Transaksi</h3>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalTransaksi }}</p>
                        </div>
                    </div>

                    <!-- Tabel Hasil -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-2xl font-bold mb-4">Detail Transaksi</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-4 font-extrabold text-gray-700">No Invoice</th>
                                        <th class="p-4 font-extrabold text-gray-700">Tanggal</th>
                                        <th class="p-4 font-extrabold text-gray-700">Pelanggan</th>
                                        <th class="p-4 font-extrabold text-gray-700">Total</th>
                                        <th class="p-4 font-extrabold text-gray-700">Dibayar</th>
                                        <th class="p-4 font-extrabold text-gray-700">Status Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksis as $transaksi)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-4 font-semibold text-gray-800">{{ $transaksi->no_invoice }}</td>
                                            <td class="p-4 font-semibold text-gray-600">
                                                {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d M Y') }}</td>
                                            <td class="p-4 font-semibold text-gray-600">
                                                {{ $transaksi->pelanggan->name ?? 'N/A' }}</td>
                                            <td class="p-4 font-semibold text-gray-600">Rp
                                                {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                            <td class="p-4 font-semibold text-green-600">Rp
                                                {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td class="p-4"><span class="px-3 py-1 text-sm rounded-full font-medium"
                                                    :class="{ 'bg-red-100 text-red-800': '{{ $transaksi->status_pembayaran }}' === 'Belum Lunas', 'bg-green-100 text-green-800': '{{ $transaksi->status_pembayaran }}' === 'Lunas' }">{{ $transaksi->status_pembayaran }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-6 text-gray-500">Tidak ada data transaksi pada
                                                periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection