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
                {{-- ... (Profil Admin) ... --}}
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 lg:p-10">

            <!-- Tombol Kembali -->
            <div class="mb-6">
                <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-2 text-lg text-gray-600 hover:text-blue-600 font-semibold w-fit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Pelanggan
                </a>
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
                <h3 class="text-2xl font-bold mb-4">Detail Riwayat Transaksi</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">No Invoice</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Tanggal</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Layanan</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Deskripsi</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Total</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ...">Status Bayar</th>
                                <th class="p-3 text-sm md:p-4 md:text-base ... text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->no_invoice }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d M Y') }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->layanan->name ?? 'N/A' }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->deskripsi ?? '-' }}</td>
                                <td class="p-3 text-sm md:p-4 md:text-base ...">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                <td class="p-3 md:p-4">
                                    <span class="px-3 py-1 text-xs md:text-sm rounded-full ...">
                                        {{ $transaksi->status_pembayaran }}
                                    </span>
                                </td>
                                <td class="p-3 md:p-4 flex justify-center items-center space-x-1 md:space-x-2">
                                    @php $transaksiData = json_encode($transaksi); @endphp
                                    <button @click="$dispatch('open-edit-modal', {{ $pelangganData }})" class="bg-green-100 ... py-1 px-3 md:py-2 md:px-6 text-sm md:text-base">Update</button>
                                    <button @click="openDeleteModal = true; deleteUrl = '{{ route('pelanggan.destroy', $transaksi->id) }}';" class="bg-red-100 ... py-1 px-3 md:py-2 md:px-6 text-sm md:text-base">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center p-6 text-gray-500">Pelanggan ini belum memiliki riwayat transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Modal edit dan hapus transaksi di-include di sini --}}
            @include('components.modal.transaksi.edit-transaksi')
            @include('components.modal.transaksi.delete-transaksi')
        </main>
    </div>
@endsection