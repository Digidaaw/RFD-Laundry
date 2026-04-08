@extends('layout.admin.layout')
@section('title', 'Kelola Layanan')

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
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Kelola Layanan</h2>
            </div>
            <div class="flex items-center gap-8">
                {{-- ... (Profil Admin) ... --}}
            </div>
        </header>

        <main class="p-6 lg:p-10">
            <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                <button @click="openAddModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 md:px-6 md:py-3 rounded-lg shadow-md w-full lg:w-auto font-semibold">
                    + Tambah Layanan
                </button>
                <div class="flex items-center space-x-2 text-gray-700 font-bold text-base md:text-lg w-full lg:w-auto">
                    <img src="{{ asset('assets/filter.svg') }}" alt="Filter Icon" class="hidden lg:block">
                    <span class="pl-2 hidden lg:block">Cari</span>
                    <div class="relative w-full">
                        <form action="{{ route('layanan.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Nama Layanan..." value="{{ $search ?? '' }}"
                                class="bg-gray-100 rounded-full py-2 pl-10 md:py-3 md:pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full text-sm md:text-base">
                            <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4 md:h-5 md:w-5">
                        </form>
                    </div>
                </div>
            </div>


            <!-- Daftar Layanan -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <!-- Desktop: Tabel, Mobile: Kartu -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-4 text-lg font-extrabold text-gray-700">ID</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700">Gambar</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700">Nama Layanan</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700">Deskripsi</th>
                                <th class="p-4 text-lg font-extrabold text-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($layanans as $layanan)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-4 font-semibold text-gray-600">{{ $loop->iteration }}</td>
                                    <td class="p-4">
                                        @if (isset($layanan->gambar[0]))
                                            <div class="w-20 h-20 rounded-md overflow-hidden bg-gray-100">
                                                <img src="{{ asset('images/layanan/' . $layanan->gambar[0]) }}"
                                                    alt="{{ $layanan->name, $layanan->unit_satuan }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div
                                                class="w-20 h-20 bg-gray-200 rounded-md flex items-center justify-center text-sm text-gray-500">
                                                No Image
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4 font-semibold text-gray-600">
                                        {{ $layanan->name }}
                                        <span class="text-sm text-gray-500">
                                            ({{ $layanan->unit_satuan ?? '-' }})
                                        </span>
                                    </td>
                                    <td class="p-4 font-semibold text-gray-600">
                                        {{ Str::limit($layanan->deskripsi, 50) }}
                                    </td>
                                    <td class="p-4 flex justify-center items-center space-x-2">
                                        @php
                                            $layananData = json_encode([
                                                'name' => $layanan->name,
                                                'deskripsi' => $layanan->deskripsi,
                                                'gambar' => $layanan->gambar ?? [],
                                                'units' => $layanan->units->map(function ($u) {
                                                    return [
                                                        'unit_satuan' => $u->unit_satuan,
                                                        'harga' => $u->harga,
                                                    ];
                                                }),
                                                'url' => route('layanan.update', $layanan->id),
                                            ]);
                                        @endphp

                                        <button @click="$dispatch('open-edit-modal', {{ $layananData }})"
                                            class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200">
                                            Update
                                        </button>
                                        <button
                                            @click="openDeleteModal = true; deleteUrl = '{{ route('layanan.destroy', $layanan->id) }}';"
                                            class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-6 text-gray-500">
                                        Belum ada data layanan yang bisa ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden space-y-4">
                    @forelse($layanans as $layanan)
                        <div class="border rounded-xl p-4 flex gap-3">
                            <div class="w-20 h-20 rounded-md overflow-hidden bg-gray-100 flex-shrink-0">
                                @if (isset($layanan->gambar[0]))
                                    <img src="{{ asset('images/layanan/' . $layanan->gambar[0]) }}"
                                        alt="{{ $layanan->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">
                                        No Image
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ $layanan->name }}
                                        <span class="text-xs text-gray-500">
                                            ({{ $layanan->unit_satuan ?? '-' }})
                                        </span>
                                    </p>
                                    <p class="font-bold text-blue-600 text-sm mt-1">
                                        Rp {{ number_format($layanan->harga, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ Str::limit($layanan->deskripsi, 60) }}
                                    </p>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    @php
                                        $layananData = json_encode([
                                            'name' => $layanan->name,
                                            'harga' => $layanan->harga,
                                            'deskripsi' => $layanan->deskripsi,
                                            'gambar' => $layanan->gambar ?? [],
                                            'url' => route('layanan.update', $layanan->id),
                                        ]);
                                    @endphp
                                    <button @click="$dispatch('open-edit-modal', {{ $layananData }})"
                                        class="flex-1 bg-green-100 text-green-700 font-semibold py-2 rounded-md text-xs">
                                        Update
                                    </button>
                                    <button
                                        @click="openDeleteModal = true; deleteUrl = '{{ route('layanan.destroy', $layanan->id) }}';"
                                        class="flex-1 bg-red-100 text-red-700 font-semibold py-2 rounded-md text-xs">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4 text-sm">
                            Belum ada data layanan yang bisa ditampilkan.
                        </p>
                    @endforelse
                </div>
            </div>

            @include('components.modal.layanan.add-layanan')
            @include('components.modal.layanan.edit-layanan')
            @include('components.modal.layanan.delete-layanan')
        </main>
    </div>
@endsection
