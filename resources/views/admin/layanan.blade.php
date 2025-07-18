@extends('layout.admin.layout')
@section('title', 'Kelola Latanan')
@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-full min-h-screen overflow-hidden relative">

            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Main Area -->
            <div class="ml-[347px]">
                <!-- Topbar -->
                <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                    <h2 class="text-4xl font-semibold text-[#151d48]">Kelola Layanan</h2>
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
                <main class="p-10" x-data="{ 
                                            openAddModal: {{ $errors->any() ? 'true' : 'false' }},
                                            openEditModal: false,
                                            openDeleteModal: false,
                                            editData: {},
                                            deleteUrl: ''
                                        }">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <button @click="openAddModal = true"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md">
                                + Tambah Layanan
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

                    <!-- Table Kasir -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">ID</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">Gambar</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">Nama Layanan</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">Deskripsi</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">Harga</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($layanans as $layanan)
                                        {{-- Inisialisasi Alpine.js untuk setiap baris --}}
                                        <tr class="border-b hover:bg-gray-50" x-data="{ showPassword: false }">
                                            <td class="p-4 font-semibold text-gray-600">{{ $loop->iteration }}</td>
                                            <td class="p-4">
                                                @if(isset($layanan->gambar[0]))
                                                    <img src="{{ asset('images/layanan/' . $layanan->gambar[0]) }}"
                                                        alt="{{ $layanan->name }}" class="w-20 h-20 object-cover rounded-md">
                                                @else
                                                    <div
                                                        class="w-20 h-20 bg-gray-200 rounded-md flex items-center justify-center text-sm text-gray-500">
                                                        No Image</div>
                                                @endif
                                            </td>
                                            <td class="p-4 font-semibold text-gray-600">{{ $layanan->name }}</td>
                                            <td class="p-4 font-semibold text-gray-600">Rp
                                                {{ number_format($layanan->harga, 0, ',', '.') }}
                                            </td>
                                            <td class="p-4 font-semibold text-gray-600">
                                                {{ Str::limit($layanan->deskripsi, 50) }}
                                            </td>
                                            <td class="p-4 flex justify-center items-center space-x-2">
                                                @php
                                                    $layananData = json_encode([
                                                        'name' => $layanan->name,
                                                        'harga' => $layanan->harga,
                                                        'deskripsi' => $layanan->deskripsi,
                                                        'gambar' => $layanan->gambar ?? [],
                                                        'url' => route('layanan.update', $layanan->id)
                                                    ]);
                                                @endphp
                                                <button @click="$dispatch('open-edit-modal', {{ $layananData }})"
                                                    class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200">Update</button>
                                                <button
                                                    @click="openDeleteModal = true; deleteUrl = '{{ route('layanan.destroy', $layanan->id) }}';"
                                                    class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-6 text-gray-500">
                                                Belum ada data layanan yang bisa ditampilkan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Include Semua Modal -->
                    @include('components.modal.layanan.add-layanan')
                    @include('components.modal.layanan.edit-layanan')
                    @include('components.modal.layanan.delete-layanan')
                </main>
            </div>
        </div>
    </div>
@endsection