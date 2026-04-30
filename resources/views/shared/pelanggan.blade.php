@extends('layout.admin.layout')
@section('title', 'Kelola Pelanggan')

@section('content')
    <div>
        <header class="w-full h-[120px] sticky top-0 z-50 bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Kelola Pelanggan</h2>
            </div>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <p class="uppercase font-semibold text-sm text-gray-900">{{ Auth::user()->role ?? 'Panel' }}</p>
                </div>
            </div>
        </header>

        <main class="p-6 lg:p-10" x-data="{
            openAddModal: {{ $errors->any() ? 'true' : 'false' }}
        }">

            <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                {{-- Tombol ini mengacu ke x-data di <main> --}}
                <button @click="openAddModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md w-full lg:w-auto">
                    + Tambah Pelanggan
                </button>
                <div
                    class="flex items-center flex-wrap gap-3 text-gray-700 font-bold text-base md:text-lg w-full lg:w-auto">
                    <form action="{{ route('pelanggan.index') }}" method="GET" class="flex items-center gap-3 w-full">
                        <div class="relative">
                            <details class="relative group">
                                <summary
                                    class="list-none flex items-center justify-center gap-2 bg-gray-100 rounded-full px-4 py-3 cursor-pointer hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <img src="{{ asset('assets/filter.svg') }}" class="h-5 w-5">
                                    <span class="text-sm font-semibold">Filter</span>
                                </summary>
                                <div
                                    class="absolute right-0 top-full mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-lg z-10 overflow-hidden">

                                    <button type="button"
                                        onclick="this.closest('form').sort.value=''; this.closest('form').submit();"
                                        class="w-full text-left px-4 py-3 hover:bg-gray-100 text-sm">
                                        Semua
                                    </button>
                                    <button type="button"
                                        onclick="this.closest('form').sort.value='updated_latest'; this.closest('form').submit();"
                                        class="w-full text-left px-4 py-3 hover:bg-gray-100 text-sm">
                                        Update Terbaru
                                    </button>
                                    <button type="button"
                                        onclick="this.closest('form').sort.value='updated_oldest'; this.closest('form').submit();"
                                        class="w-full text-left px-4 py-3 hover:bg-gray-100 text-sm">
                                        Update Terlama
                                    </button>
                                </div>
                            </details>
                        </div>
                        <div class="relative flex-1 min-w-0">
                            <input type="text" name="search" placeholder="Nama/Kontak..." value="{{ $search ?? '' }}"
                                class="bg-gray-100 rounded-full py-2 pl-12 md:py-3 md:pl-14 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full text-sm md:text-base">
                            <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4 md:h-5 md:w-5">
                        </div>
                        <input type="hidden" name="sort" value="{{ $sort ?? 'updated_latest' }}">
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-4 text-lg font-extrabold text-gray-700">#</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700">Nama Pelanggan</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700">Kontak</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggans as $pelanggan)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-semibold text-gray-600">{{ $loop->iteration }}</td>
                                    <td class="p-4 font-semibold text-gray-800">{{ $pelanggan->name }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ $pelanggan->kontak }}</td>
                                    <td class="p-4 flex justify-center items-center space-x-2">
                                        @php
                                            $pelangganData = json_encode([
                                                'name' => $pelanggan->name,
                                                'kontak' => $pelanggan->kontak,
                                                'url' => route('pelanggan.update', $pelanggan->id),
                                            ]);
                                        @endphp
                                        <a href="{{ route('report.pelanggan', $pelanggan->id) }}"
                                            class="bg-blue-100 text-blue-700 font-bold py-2 px-6 rounded-md hover:bg-blue-200 transition">
                                            Laporan
                                        </a>

                                        <button
                                            @click="$dispatch('open-edit-customer-modal', {{ json_encode([
                                                'name' => $pelanggan->name,
                                                'kontak' => $pelanggan->kontak,
                                                'url' => route('pelanggan.update', $pelanggan->id),
                                            ]) }})"
                                            class="bg-green-100 text-green-700 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-green-200 transition text-sm md:text-base">
                                            Update
                                        </button>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-6 text-gray-500">Belum ada data pelanggan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-center">
                    {{ $pelanggans->links() }}
                </div>
            </div>

            @include('components.modal.customer.add-customer')
            @include('components.modal.customer.edit-customer')
        </main>
    </div>
@endsection
