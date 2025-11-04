@extends('layout.admin.layout')
@section('title', 'Laporan Piutang')

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">
            @include('components.sidebar')
            <div class="ml-[347px]">
                <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                    <h2 class="text-4xl font-semibold text-[#151d48]">Laporan Piutang Pelanggan</h2>
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
                    <!-- Form Pencarian -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                        <form action="{{ route('report.piutang') }}" method="GET">
                            <div class="flex items-center">
                                <div class="relative w-full">
                                    <input type="text" name="search"
                                        placeholder="Cari berdasarkan nama atau kontak pelanggan..."
                                        value="{{ request('search') }}"
                                        class="w-full bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                </div>
                                <button type="submit"
                                    class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full shadow-md">Cari</button>
                            </div>
                        </form>
                    </div>

                    <!-- Ringkasan Data -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-lg font-semibold text-gray-500">Total Piutang (Hasil Pencarian)</h3>
                            <p class="text-3xl font-bold text-red-600 mt-2">Rp
                                {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-md">
                            <h3 class="text-lg font-semibold text-gray-500">Jumlah Transaksi Yang Masih Hutang</h3>
                            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $piutangs->count() }}</p>
                        </div>
                    </div>

                    <!-- Tabel Hasil -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-2xl font-bold mb-4">Detail Piutang</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-4 font-extrabold text-gray-700">No Invoice</th>
                                        <th class="p-4 font-extrabold text-gray-700">Tanggal</th>
                                        <th class="p-4 font-extrabold text-gray-700">Nama Pelanggan</th>
                                        <th class="p-4 font-extrabold text-gray-700">Kontak</th>
                                        <th class="p-4 font-extrabold text-gray-700">Sisa Bayar</th>
                                        <th class="p-4 font-extrabold text-gray-700 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($piutangs as $piutang)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-4 font-semibold text-gray-800">{{ $piutang->no_invoice }}</td>
                                            <td class="p-4 font-semibold text-gray-600">
                                                {{ \Carbon\Carbon::parse($piutang->tanggal_order)->format('d M Y') }}</td>
                                            <td class="p-4 font-semibold text-gray-600">{{ $piutang->pelanggan->name ?? 'N/A' }}
                                            </td>
                                            <td class="p-4 font-semibold text-gray-600">
                                                {{ $piutang->pelanggan->kontak ?? 'N/A' }}</td>
                                            <td class="p-4 font-bold text-red-600">Rp
                                                {{ number_format($piutang->sisa_bayar, 0, ',', '.') }}</td>
                                            <td class="p-4 text-center">
                                                @php
                                                    $piutangData = json_encode([
                                                        'name' => $piutang->pelanggan->name ?? 'N/A',
                                                        'sisa_bayar' => $piutang->sisa_bayar,
                                                        'url' => route('transaksi.bayar', $piutang->id)
                                                    ]);
                                                @endphp
                                                <button @click="$dispatch('open-bayar-modal', {{ $piutangData }})"
                                                    class="bg-green-100 text-green-800 font-bold py-2 px-6 rounded-md hover:bg-green-200">
                                                    Bayar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-6 text-gray-500">
                                                @if(request('search'))
                                                    Tidak ada data piutang yang cocok dengan pencarian "{{ request('search') }}".
                                                @else
                                                    Luar biasa! Tidak ada pelanggan yang berhutang.
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Include modal pembayaran --}}
                    @include('components.modal.bayar-piutang')
                </main>
            </div>
        </div>
    </div>
@endsection