@extends('layout.admin.layout')
@section('title', 'Kelola Kasir')

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
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Kelola Kasir</h2>
            </div>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <p class="uppercase font-semibold text-sm text-gray-900">{{ Auth::user()->role ?? 'Panel' }}</p>
                </div>
            </div>
        </header>

        <main class="p-6 lg:p-10">
            <div class="flex flex-col lg:flex-row justify-between items-center mb-8 gap-4">
                <button @click="openAddModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 md:px-6 md:py-3 rounded-lg shadow-md w-full lg:w-auto font-semibold">
                    + Tambah Kasir
                </button>
                <div
                    class="flex items-center flex-wrap gap-3 text-gray-700 font-bold text-base md:text-lg w-full lg:w-auto">
                    <form action="{{ route('kasir.index') }}" method="GET" class="flex items-center gap-3 w-full">
                        <div class="relative">
                            <details class="relative group">
                                <summary
                                    class="list-none flex items-center justify-center bg-gray-100 rounded-full p-3 cursor-pointer hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <img src="{{ asset('assets/filter.svg') }}" alt="Sort Icon" class="h-5 w-5">
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
                            <input type="text" name="search" placeholder="Nama Layanan..." value="{{ $search ?? '' }}"
                                class="bg-gray-100 rounded-full py-2 pl-12 md:py-3 md:pl-14 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full text-sm md:text-base">
                            <img src="{{ asset('assets/search-icon.svg') }}" alt="Search Icon"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 h-4 w-4 md:h-5 md:w-5">
                        </div>
                        <input type="hidden" name="sort" value="{{ $sort ?? 'updated_latest' }}">
                    </form>
                </div>
            </div>


            <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-sm md:p-4 md:text-lg font-extrabold text-gray-700">#</th>
                                <th class="p-3 text-sm md:p-4 md:text-lg font-extrabold text-gray-700">Nama</th>
                                <th class="p-3 text-sm md:p-4 md:text-lg font-extrabold text-gray-700">Username</th>
                                <th class="p-3 text-sm md:p-4 md:text-lg font-extrabold text-gray-700">Password</th>
                                <th class="p-3 text-sm md:p-4 md:text-lg font-extrabold text-gray-700 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kasirs as $kasir)
                                <tr class="border-b hover:bg-gray-50" x-data="{ showPassword: false }">
                                    <td class="p-4 font-semibold text-gray-600">{{ $loop->iteration }}</td>
                                    <td class="p-4 font-semibold text-gray-800">{{ $kasir->name }}</td>
                                    <td class="p-4 font-semibold text-gray-600">{{ $kasir->username }}</td>
                                    <td class="p-4 font-semibold text-gray-600">
                                        <div class="flex items-center space-x-2">
                                            {{-- Tampilkan password asli jika showPassword true, jika tidak tampilkan
                                                bintang --}}
                                            <span
                                                x-text="showPassword ? '{{ $kasir->plain_password ?? 'Tidak Tersedia' }}' : '********'"></span>
                                            <button @click="showPassword = !showPassword"
                                                class="text-gray-500 hover:text-gray-700">
                                                <!-- Ikon Mata Terbuka -->
                                                <svg x-show="!showPassword" class="h-5 w-5"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.432 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <!-- Ikon Mata Tertutup -->
                                                <svg x-show="showPassword" class="h-5 w-5"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" style="display: none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-4 flex justify-center items-center space-x-2">
                                        <button
                                            @click="
                                                                    openEditModal = true;
                                                                    editData = { 
                                                                        name: '{{ addslashes($kasir->name) }}', 
                                                                        username: '{{ $kasir->username }}',
                                                                        url: '{{ route('users.update', $kasir->id) }}'
                                                                    };
                                                                "
                                            class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">
                                            Update
                                        </button>
                                        <button
                                            @click="
                                                                    openDeleteModal = true;
                                                                    deleteUrl = '{{ route('users.destroy', $kasir->id) }}';
                                                                "
                                            class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-6 text-gray-500">
                                        Belum ada data kasir yang bisa ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-center">
                {{ $kasirs->links() }}
            </div>

            @include('components.modal.kasir.add-kasir')
            @include('components.modal.kasir.edit-kasir')
            @include('components.modal.kasir.delete-kasir')
        </main>
    </div>
@endsection
