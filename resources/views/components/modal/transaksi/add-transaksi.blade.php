<div x-show="openAddModal" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div @click.away="openAddModal = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-lg shadow-lg relative max-h-[90vh] flex flex-col"
         x-data="{
            potongan: 0,
            potonganDisplay: '',
            bayar: 0,
            bayarDisplay: '',
            deskripsi: '',
            layanans: {{ json_encode($layanans) }},
            layanansObj: {},
            items: [{ id_layanan: '', berat: 0 }],
            bayarError: '',
            addItem() {
                this.items.push({ id_layanan: '', berat: 0 });
            },
            removeItem(index) {
                if (this.items.length <= 1) return;
                this.items.splice(index, 1);
            },
            formatRp(value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
            },
            get subtotal() {
                return this.items.reduce((sum, row) => {
                    const layanan = this.layanansObj[row.id_layanan];
                    const berat = Number(row.berat || 0);
                    if (!layanan || berat <= 0) return sum;
                    return sum + (Number(layanan.harga) * berat);
                }, 0);
            },
            get total() {
                let t = this.subtotal - Number(this.potongan || 0);
                return t < 0 ? 0 : t;
            },
            get sisa() {
                let s = this.total - Number(this.bayar || 0);
                return s < 0 ? 0 : s;
            },
            onPotonganInput(raw) {
                // Hanya angka
                const cleaned = String(raw).replace(/[^0-9]/g, '');
                const num = cleaned === '' ? 0 : parseInt(cleaned, 10);
                this.potongan = num;
                this.potonganDisplay = num ? num.toLocaleString('id-ID') : '';
            },
            onBayarInput(raw) {
                const cleaned = String(raw).replace(/[^0-9]/g, '');
                let num = cleaned === '' ? 0 : parseInt(cleaned, 10);
                // Batasi maksimal ke total (tidak boleh lebih dari tagihan)
                const max = this.total || 0;
                if (num > max) num = max;
                this.bayar = num;
                this.bayarDisplay = num ? num.toLocaleString('id-ID') : '';
                this.validateBayar();
            },
            validateBayar() {
                const bayarNum = Number(this.bayar || 0);
                if (bayarNum > this.total) {
                    this.bayarError = 'Jumlah bayar tidak boleh melebihi total.';
                } else {
                    this.bayarError = '';
                }
            },
            init() {
                this.layanansObj = Object.fromEntries(this.layanans.map(l => [String(l.id), l]));

                // Inisialisasi Choices.js sekali saat komponen siap
                this.$nextTick(() => {
                    const element = this.$refs.pelangganSelect;
                    if (!element) return;

                    // Hindari inisialisasi ganda
                    if (element._choicesInstance) return;

                    element._choicesInstance = new Choices(element, {
                        searchEnabled: true,
                        itemSelectText: 'Pilih',
                        searchFields: ['label'],
                        shouldSort: true,
                    });
                });
            }
         }">
        <button @click="openAddModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center flex-shrink-0">Tambah Transaksi Baru</h2>

        <div class="overflow-y-auto flex-grow pr-2">
        <form method="POST" action="{{ route('transaksi.store') }}">
            @csrf
            <div class="col-span-2">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Pelanggan</label>
                <select name="id_pelanggan" x-ref="pelangganSelect" class="@error('id_pelanggan') border-red-500 @enderror" required>
                    <option value="" disabled {{ old('id_pelanggan') ? '' : 'selected' }}>Ketik untuk mencari pelanggan...</option>
                    @foreach($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->id }}" {{ old('id_pelanggan') == $pelanggan->id ? 'selected' : '' }}>
                            {{ $pelanggan->name }} - {{ $pelanggan->kontak }}
                        </option>
                    @endforeach
                </select>
                @error('id_pelanggan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi (Keyword)</label>
                <input type="text"
                       x-model="deskripsi"
                       name="deskripsi"
                       value="{{ old('deskripsi') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('deskripsi') border-red-500 @enderror"
                       placeholder="Contoh: Baju Pesta, Selimut Tebal">
                @error('deskripsi')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-gray-700 text-lg font-semibold">Layanan</label>
                    <button type="button" @click="addItem()"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                        + Tambah Layanan
                    </button>
                </div>

                <template x-for="(row, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-3 items-end mb-3 p-3 rounded-lg border border-gray-200">
                        <div class="col-span-7">
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Pilih Layanan</label>
                            <select :name="`items[${index}][id_layanan]`" x-model="row.id_layanan"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white"
                                    required>
                                <option value="">Pilih Layanan</option>
                                @foreach($layanans as $layanan)
                                    <option value="{{ $layanan->id }}">{{ $layanan->name }} (Rp {{ number_format($layanan->harga, 0, ',', '.') }}/kg)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-gray-700 text-sm font-semibold mb-1">Berat</label>
                            <input type="number" step="0.1" min="0" :name="`items[${index}][berat]`" x-model.number="row.berat"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                   required>
                        </div>
                        <div class="col-span-2 flex justify-end">
                            <button type="button" @click="removeItem(index)"
                                    class="px-3 py-2 border rounded-lg font-semibold bg-red-50 text-red-700 border-red-200 hover:bg-red-100"
                                    :class="{ 'opacity-50 cursor-not-allowed hover:bg-red-50': items.length <= 1 }"
                                    :disabled="items.length <= 1">
                                Hapus
                            </button>
                        </div>

                        <div class="col-span-12 flex justify-between text-sm text-gray-600">
                            <span>Subtotal item:</span>
                            <span x-text="formatRp((layanansObj[row.id_layanan]?.harga || 0) * (Number(row.berat || 0)))"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Potongan (Rp)</label>
                    <input type="hidden" name="potongan" :value="potongan">
                    <input type="text"
                           x-model="potonganDisplay"
                           x-on:input="onPotonganInput($event.target.value)"
                           inputmode="numeric"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           placeholder="0">
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar</label>
                    <input type="hidden" name="jumlah_bayar" :value="bayar">
                    <input type="text"
                           x-model="bayarDisplay"
                           x-on:input="onBayarInput($event.target.value)"
                           inputmode="numeric"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           placeholder="0">
                    <p x-show="bayarError"
                       x-text="bayarError"
                       class="text-red-600 text-sm mt-1"></p>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded-lg space-y-2">
                <div class="flex justify-between text-md text-gray-600">
                    <span>Subtotal:</span>
                    <span x-text="formatRp(subtotal)"></span>
                </div>
                <div class="flex justify-between text-md text-gray-600">
                    <span>Potongan:</span>
                    <span class="text-red-600" x-text="'- ' + formatRp(potongan)"></span>
                </div>
                <hr>
                <div class="flex justify-between font-bold text-lg">
                    <span>Total Harga:</span>
                    <span x-text="formatRp(total)"></span>
                </div>
                <div class="flex justify-between font-medium text-md" :class="{ 'text-red-600': sisa > 0, 'text-green-600': sisa <= 0 }">
                    <span>Sisa Bayar:</span>
                    <span x-text="formatRp(sisa)"></span>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-6 pb-1">
                <button type="button" @click="openAddModal = false" class="px-4 py-2 border rounded-lg">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Simpan Transaksi</button>
            </div>
        </form>
        </div>
    </div>
</div>