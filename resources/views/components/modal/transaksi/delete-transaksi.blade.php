<!-- resources/views/components/modal/transaksi/delete-transaksi.blade.php -->
<div x-show="openDeleteModal" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div @click.away="openDeleteModal = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center">
        <h2 class="text-2xl font-bold mb-4">Hapus Transaksi?</h2>
        <p class="text-gray-600 mb-6">Tindakan ini akan menghapus data transaksi secara permanen.</p>
        <form x-bind:action="deleteUrl" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-center gap-4">
                <button type="button" @click="openDeleteModal = false" class="px-6 py-2 ...">Batal</button>
                <button type="submit" class="px-6 py-2 ...">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>