<div x-data="{
        open: false,
        data: {},
        choicesInstance: null,
        
        init() {
            this.$watch('open', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        const element = this.$refs.pelangganSelectEdit;
                        if (!element) return;
                        if (this.choicesInstance) this.choicesInstance.destroy();
                        this.choicesInstance = new Choices(element, {
                            searchEnabled: true,
                            itemSelectText: 'Pilih',
                            searchFields: ['label'],
                            shouldSort: true,
                        });
                        if (this.data.id_pelanggan) {
                            setTimeout(() => {
                                if (this.choicesInstance) {
                                    this.choicesInstance.setChoiceByValue(String(this.data.id_pelanggan));
                                }
                            }, 50);
                        }
                    });
                } else {
                    if (this.choicesInstance) {
                        this.choicesInstance.destroy();
                        this.choicesInstance = null;
                    }
                }
            });
        },
        
        openModal(event) {
            let detail = event.detail;
            if (detail.tanggal_order) {
                detail.tanggal_order = detail.tanggal_order.substring(0, 10);
            }
            this.data = detail;
            this.open = true;
        }
     }"
     @open-edit-modal.window="openModal($event)"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <div @click.away="open = false" x-transition
        class="bg-white p-8 rounded-xl w-full max-w-lg shadow-lg relative max-h-[90vh] flex flex-col">
        <button @click="open = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold z-10">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center flex-shrink-0">Update Transaksi</h2>

        <div class="overflow-y-auto flex-grow pr-2">
            <form x-bind:action="data.id ? '{{ url('transaksi') }}/' + data.id : ''" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="_redirect_url" :value="window.location.href.includes('report') ? window.location.href : ''">

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Tanggal Order</label>
                    <input type="date" name="tanggal_order" x-model="data.tanggal_order"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Pelanggan</label>
                    @if(isset($pelanggans))
                        <select name="id_pelanggan" x-ref="pelangganSelectEdit">
                            <option value="" disable selected>Pilih Pelanggan...</option>
                            @foreach($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}">{{ $pelanggan->name }} - {{ $pelanggan->kontak }}</option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-red-500">Error: Data pelanggan tidak ditemukan.</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi (Keyword)</label>
                    <textarea name="deskripsi" x-model="data.deskripsi" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                              placeholder="Contoh: Baju Pesta, Selimut Tebal"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Status Order</label>
                    <select name="status_order" x-model="data.status_order"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="Baru">Baru</option>
                        <option value="Proses">Proses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Diambil">Diambil</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" x-model="data.jumlah_bayar"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <small x-show="data.total_harga" class="text-gray-500" x-text="'Total Tagihan: Rp ' + new Intl.NumberFormat('id-ID').format(data.total_harga)"></small>
                </div>

                <div class="flex justify-end gap-4 mt-8 pt-4 border-t sticky bottom-0 bg-white">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>