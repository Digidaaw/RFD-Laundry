<!-- Modal Konfirmasi Hapus -->
<div x-show="openDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div @click.away="openDeleteModal = false" x-transition
        class="bg-white p-8 rounded-xl w-full max-w-md shadow-lg text-center">
        
        <h2 class="text-2xl font-bold mb-4">Anda Yakin?</h2>
        <p class="text-gray-600 mb-6">
            Data kasir ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>

        <form x-bind:action="deleteUrl" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-center gap-4">
                <button type="button" @click="openDeleteModal = false"
                    class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>
