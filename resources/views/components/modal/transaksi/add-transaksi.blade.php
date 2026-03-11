<div x-show="openAddModal" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div @click.away="openAddModal = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-lg shadow-lg relative"
         x-data="{ 
            selectedLayanan: null, 
            berat: 0, 
            potongan: 0,
            bayar: 0,
            layanans: {{ json_encode($layanans) }},
            layanansObj: {},
            get subtotal() {
                const layanan = this.layanansObj[this.selectedLayanan];
                return layanan && this.berat > 0 ? layanan.harga * this.berat : 0;
            },
            get total() {
                let t = this.subtotal - this.potongan;
                return t < 0 ? 0 : t;
            },
            get sisa() {
                let s = this.total - this.bayar;
                return s < 0 ? 0 : s;
            },
            init() {
                // create lookup table once
                this.layanansObj = Object.fromEntries(this.layanans.map(l => [l.id, l]));

                this.$watch('openAddModal', (value) => {
                    if (value) {
                        // reset state each time modal opens so previous inputs don't linger
                        this.selectedLayanan = null;
                        this.berat = 0;
                        this.potongan = 0;
                        this.bayar = 0;

                        this.$nextTick(() => {
                            const element = this.$refs.pelangganSelect;
                            if (element.choices) element.choices.destroy();
                            
                            // PERBAIKAN: Konfigurasi Choices.js yang lebih bersih
                            new Choices(element, {
                                searchEnabled: true,
                                itemSelectText: 'Pilih',
                                searchFields: ['label'],
                                shouldSort: true, // Memastikan daftar selalu terurut
                                // 'placeholder' dan 'placeholderValue' dihapus dari sini
                            });
                        });
                    }
                });
            }
         }">
        <button @click="openAddModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center">Tambah Transaksi Baru</h2>
        <form method="POST" action="{{ route('transaksi.store') }}">
            @csrf
            <div class="col-span-2">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Pelanggan</label>
                <select name="id_pelanggan" x-ref="pelangganSelect">
                    {{-- PERBAIKAN: Menambahkan 'disabled selected' pada placeholder --}}
                    <option value="" disabled selected>Ketik untuk mencari pelanggan...</option>
                    @foreach($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->id }}">{{ $pelanggan->name }} - {{ $pelanggan->kontak }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mt-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi (Keyword)</label>
                <input type="text" x-model="deskripsi "name="deskripsi" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Contoh: Baju Pesta, Selimut Tebal">
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Layanan</label>
                    <select name="id_layanan" x-model="selectedLayanan" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white">
                        <option value="">Pilih Layanan</option>
                        @foreach($layanans as $layanan)
                            <option value="{{ $layanan->id }}">{{ $layanan->name }} (Rp {{ number_format($layanan->harga, 0, ',', '.') }}/kg)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Berat (kg/pcs)</label>
                    <input type="number" step="0.1" name="berat_laundry" x-model.number="berat" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Potongan (Rp)</label>
                    <input type="number" name="potongan" x-model.number="potongan" value="0" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="0">
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" x-model.number="bayar" value="0" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="0">
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded-lg space-y-2">
                <div class="flex justify-between text-md text-gray-600">
                    <span>Subtotal:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                </div>
                <div class="flex justify-between text-md text-gray-600">
                    <span>Potongan:</span>
                    <span class="text-red-600" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(potongan)"></span>
                </div>
                <hr>
                <div class="flex justify-between font-bold text-lg">
                    <span>Total Harga:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)"></span>
                </div>
                <div class="flex justify-between font-medium text-md" :class="{ 'text-red-600': sisa > 0, 'text-green-600': sisa <= 0 }">
                    <span>Sisa Bayar:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(sisa)"></span>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" @click="openAddModal = false" class="px-4 py-2 border rounded-lg">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>