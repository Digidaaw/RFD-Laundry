@extends('layout.admin.layout')
@section('title', 'Kelola Pelanggan')

@section('content')
    <!-- Main Area -->
    <div>
        <!-- Topbar -->
        <header class="w-full h-[120px] bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Tombol ini mengacu ke x-data global di

                <body> --}}
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
                    <img src="{{ asset('img/rectangle-1393.png') }}" class="w-[60px] h-[60px] rounded-full object-cover"
                        alt="Profile">
                    <div class="hidden lg:block">
                        <p class="text-base font-medium text-gray-900">Admin</p>
                        <p class="text-sm text-gray-500">Panel</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        {{-- PERBAIKAN: x-data ini sekarang HANYA mengurus state halaman ini --}}
        <main class="p-6 lg:p-10" x-data="{
                        openAddModal: {{ $errors->any() ? 'true' : 'false' }},
                        openDeleteModal: false,
                        deleteUrl: ''
                    }">

            <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                {{-- Tombol ini mengacu ke x-data di <main> --}}
                    <button @click="openAddModal = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md w-full lg:w-auto">
                        + Tambah Pelanggan
                    </button>

                    <!-- Search -->
                    <div class="flex items-center space-x-2 text-gray-700 font-bold text-lg w-full lg:w-auto">
                        <img src="{{ asset('assets/filter.svg') }}" alt="Filter Icon" class="hidden lg:block">
                        <span class="pl-2 hidden lg:block">Cari</span>
                        <div class="relative w-full">
                            <form action="{{ route('pelanggan.index') }}" method="GET">
                                <input type="text" name="search" placeholder="Nama atau Kontak..."
                                    value="{{ $search ?? '' }}"
                                    class="bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
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
                                                                    'url' => route('pelanggan.update', $pelanggan->id)
                                                                ]);
                                                            @endphp
                                                            <a href="{{ route('report.pelanggan', $pelanggan->id) }}"
                                                                class="bg-blue-100 text-blue-700 font-bold py-2 px-6 rounded-md hover:bg-blue-200 transition">
                                                                Laporan
                                                            </a>

                                                            {{-- Tombol Update dengan Data Langsung (Inline) --}}
                                                            <button @click="$dispatch('open-edit-customer-modal', {{ json_encode([
                                    'name' => $pelanggan->name,
                                    'kontak' => $pelanggan->kontak,
                                    'url' => route('pelanggan.update', $pelanggan->id)
                                ]) }})"
                                                                class="bg-green-100 text-green-700 font-bold py-1 px-3 md:py-2 md:px-6 rounded-md hover:bg-green-200 transition text-sm md:text-base">
                                                                Update
                                                            </button>

                                                            {{-- Tombol ini mengacu ke x-data di <main> --}}
                                                                <button
                                                                    @click="openDeleteModal = true; deleteUrl = '{{ route('pelanggan.destroy', $pelanggan->id) }}';"
                                                                    class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">
                                                                    Delete
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
            </div>

            {{-- Ganti nama 'customer' menjadi 'pelanggan' agar konsisten --}}
            @include('components.modal.customer.add-customer')
            @include('components.modal.customer.edit-customer')
            @include('components.modal.customer.delete-customer')
        </main>
    </div>
@endsection