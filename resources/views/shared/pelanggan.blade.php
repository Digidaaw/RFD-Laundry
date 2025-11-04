@extends('layout.admin.layout')
@section('title', 'Kelola Pelanggan')

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">

            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Main Area -->
            <div class="ml-[347px]">
                <!-- Topbar -->
                <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                    <h2 class="text-4xl font-semibold text-[#151d48]">Kelola Pelanggan</h2>
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

                <!-- Content Area -->
                {{-- PERBAIKAN: x-data sekarang lebih sederhana, hanya untuk modal tambah & hapus --}}
                <main class="p-10" x-data="{ 
                        openAddModal: {{ $errors->any() ? 'true' : 'false' }},
                        openDeleteModal: false,
                        deleteUrl: ''
                    }">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <button @click="openAddModal = true"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md">
                                + Tambah Pelanggan
                            </button>
                        </div>

                        <!-- Search -->
                        <div class="flex items-center space-x-2 text-gray-700 font-bold text-lg">
                            <img src="{{ asset('assets/filter.svg') }}" alt="Filter Icon">
                            <span class="pl-2">Filter By</span>
                            <div class="relative">
                                <input type="text" placeholder="Search here..."
                                    class="bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-80">
                                <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            </div>
                        </div>
                    </div>

                    <!-- Table Pelanggan -->
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
                                                {{-- PERBAIKAN: Tombol Update sekarang mengirim event, bukan mengubah state --}}
                                                <button @click="$dispatch('open-edit-modal', {{ $pelangganData }})"
                                                    class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">
                                                    Update
                                                </button>
                                                <button @click="openDeleteModal = true; deleteUrl = '{{ route('pelanggan.destroy', $pelanggan->id) }}';"
                                                    class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-6 text-gray-500">
                                                Belum ada data pelanggan yang bisa ditampilkan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Include Semua Modal -->
                    @include('components.modal.customer.add-customer')
                    @include('components.modal.customer.edit-customer')
                    @include('components.modal.customer.delete-customer')
                </main>
            </div>
        </div>
    </div>
@endsection
