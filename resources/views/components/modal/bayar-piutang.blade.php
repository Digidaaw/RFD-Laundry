<!-- Modal Pembayaran Piutang -->
<div x-data="{ open: false, data: {} }" 
     @open-bayar-modal.window="open = true; data = $event.detail" 
     @keydown.escape.window="open = false" 
     x-show="open" 
     x-cloak 
     x-transition 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <div @click.away="open = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-md shadow-lg relative">
        <button @click="open = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-2 text-center">Bayar Piutang</h2>
        <p class="text-center text-gray-500 mb-6">Pelanggan: <span x-text="data.name" class="font-semibold"></span></p>
        
        <form x-bind:action="data.url" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded">
                <label class="block text-gray-700 font-semibold">Sisa Hutang</label>
                <p class="text-3xl font-bold text-red-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(data.sisa_bayar)"></p>
            </div>

            <div class="mb-4">
                <label for="bayar_sekarang" class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar Sekarang</label>
                <input type="number" id="bayar_sekarang" name="bayar_sekarang" 
                       :max="data.sisa_bayar"
                       placeholder="Masukkan nominal pembayaran" 
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="flex justify-end gap-4 mt-8">
                <button type="button" @click="open = false" class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>
