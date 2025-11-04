<!-- Modal Overlay -->
{{-- PERBAIKAN: Pastikan x-show menggunakan "openAddModal" --}}
<div x-show="openAddModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <!-- Modal Content -->
    {{-- PERBAIKAN: Pastikan @click.away dan @click pada tombol Batal/Close menggunakan "openAddModal" --}}
    <div @click.away="openAddModal = false" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="bg-white p-8 rounded-xl w-full max-w-xl shadow-lg relative">

        <!-- Close Button -->
        <button @click="openAddModal = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">
            &times;
        </button>

        <!-- Modal Title -->
        <h2 class="text-2xl font-bold mb-6 text-center">Tambah Kasir</h2>

        <!-- Form -->
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <input type="hidden" name="role" value="kasir">

            <!-- Fields (Nama, Username, Password with toggle) -->
            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Nama</label>
                <input type="text" name="name" placeholder="Masukkan nama" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror" />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Username</label>
                <input type="text" name="username" placeholder="Masukkan username" value="{{ old('username') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('username') border-red-500 @enderror" />
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4" x-data="{ showPassword: false }">
                <label for="password_add" class="block text-gray-700 text-lg font-semibold mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password_add" name="password"
                        placeholder="Masukkan password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <svg x-show="!showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.432 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" @click="openAddModal = false"
                    class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>