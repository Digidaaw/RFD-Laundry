<div x-show="openStatusModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <!-- Konten Modal -->
    <div @click.away="openStatusModal = false" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center relative">

        <button @click="openStatusModal = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">
            &times;
        </button>

        <h2 class="text-2xl font-bold mb-4" x-text="statusTitle">Ubah Status Layanan</h2>
        <p class="text-gray-600 mb-6" x-text="statusMessage">Apakah Anda yakin ingin mengubah status layanan ini?</p>

        <!-- Form Ubah Status (Method PATCH) -->
        <form x-bind:action="statusUrl" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <div class="flex justify-center gap-4">
                <!-- Tombol Batal -->
                <button type="button" @click="openStatusModal = false"
                    class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <!-- Tombol Konfirmasi -->
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Ya, Ubah
                </button>
            </div>
        </form>
    </div>
</div>
