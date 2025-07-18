<!-- Modal Konfirmasi Sign Out -->
<div x-data="{ open: false }" 
     @open-signout-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-show="open" 
     x-cloak 
     x-transition:enter="transition ease-out duration-300"
     x-transition:leave="transition ease-in duration-200"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <div @click.away="open = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center">
        <h2 class="text-2xl font-bold mb-4">Sign Out</h2>
        <p class="text-gray-600 mb-6">Anda yakin ingin keluar dari sesi ini?</p>

        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <div class="flex justify-center gap-4">
                <button type="button" @click="open = false" class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Ya, Sign Out
                </button>
            </div>
        </form>
    </div>
</div>
