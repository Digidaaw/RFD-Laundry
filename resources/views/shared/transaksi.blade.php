@extends('layout.admin.layout')
@section('title', 'Kelola Transaksi')

@section('content')
    <div>
        <header class="w-full h-[120px] bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Kelola Transaksi</h2>
            </div>
            <div class="flex items-center gap-8">
                {{-- ... (Profil Admin) ... --}}
            </div>
        </header>

        <main class="p-6 lg:p-10">
            <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                <button @click="openAddModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 md:px-6 md:py-3 rounded-lg shadow-md w-full lg:w-auto font-semibold">
                    + Tambah Transaksi
                </button>
                <div class="flex items-center space-x-2 text-gray-700 font-bold text-base md:text-lg w-full lg:w-auto">
                    <img src="{{ asset('assets/filter.svg') }}" alt="Filter Icon" class="hidden lg:block">
                    <span class="pl-2 hidden lg:block">Cari</span>
                    <div class="relative w-full">
                        <form action="{{ route('transaksi.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Nama atau Kontak..." value="{{ $search ?? '' }}"
                                class="bg-gray-100 rounded-full py-2 pl-10 md:py-3 md:pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full text-sm md:text-base">
                            <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4 md:h-5 md:w-5">
                        </form>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">No Invoice</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Tanggal</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Pelanggan</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Deskripsi</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Total</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Sisa Bayar</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Status</th>
                                <th class="p-3 text-sm md:p-4 md:text-base lg:text-lg ...">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->no_invoice }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base ...">
                                        {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d-m-Y') }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->pelanggan->name ?? 'N/A' }}
                                    </td>
                                    <td class="p-3 text-sm md:p-4 md:text-base ...">{{ $transaksi->deskripsi ?? '-' }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base ...">Rp
                                        {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="p-3 text-sm md:p-4 md:text-base ...">Rp
                                        {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}</td>
                                    <td class="p-3 md:p-4">
                                        <span class="block ... text-xs md:text-sm ...">{{ $transaksi->status_order }}</span>
                                        <span
                                            class="block ... text-xs md:text-sm ...">{{ $transaksi->status_pembayaran }}</span>
                                    </td>
                                    <td class="p-3 md:p-4 flex justify-center items-center space-x-1 md:space-x-2">
                                        @php $transaksiData = json_encode($transaksi); @endphp
                                        <button @click="$dispatch('open-edit-modal', {{ $transaksiData }})"
                                            class="bg-green-100 text-green-700 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-green-200 text-sm md:text-base transition">Update</button>
                                        <button
                                            @click="openDeleteModal = true; deleteUrl = '{{ route('transaksi.destroy', $transaksi->id) }}';"
                                            class="bg-red-100 text-red-700 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-red-200 text-sm md:text-base transition">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center p-6 text-gray-500">Tidak ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @include('components.modal.transaksi.add-transaksi')
            @include('components.modal.transaksi.edit-transaksi')
            @include('components.modal.transaksi.delete-transaksi')
        </main>
    </div>
@endsection