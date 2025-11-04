<!-- Modal Konfirmasi Sign Out -->
{{-- Modal ini dikontrol oleh 'openSignOutModal' dari layout.blade.php --}}
<div x-show="openSignOutModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <!-- Konten Modal -->
    <div @click.away="openSignOutModal = false" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center">
        
        <h2 class="text-2xl font-bold mb-4">Konfirmasi Sign Out</h2>
        <p class="text-gray-600 mb-6">
            Anda yakin ingin keluar dari sesi ini?
        </p>

        <!-- Form untuk Logout (Method POST) -->
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <div class="flex justify-center gap-4">
                <!-- Tombol Batal -->
                <button type="button" @click="openSignOutModal = false"
                    class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <!-- Tombol Sign Out -->
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Ya, Sign Out
                </button>
            </div>
        </form>
    </div>
</div>

