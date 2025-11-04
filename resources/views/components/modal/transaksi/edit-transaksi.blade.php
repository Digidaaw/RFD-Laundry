<!-- Modal Edit Transaksi -->
<div x-data="{ open: false, data: {} }" 
     @open-edit-modal.window="open = true; data = $event.detail" 
     x-show="open" 
     x-cloak 
     x-transition 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <div @click.away="open = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-md shadow-lg relative">
        <button @click="open = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center">Update Transaksi</h2>
        
        <form x-bind:action="data.id ? '{{ url('transaksi') }}/' + data.id : ''" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Status Order</label>
                <select name="status_order" x-model="data.status_order" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="Baru">Baru</option>
                    <option value="Proses">Proses</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Diambil">Diambil</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Jumlah Bayar</label>
                <input type="number" name="jumlah_bayar" x-model="data.jumlah_bayar" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
