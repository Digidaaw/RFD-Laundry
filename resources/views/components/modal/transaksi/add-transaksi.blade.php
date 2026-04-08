<div x-show="openAddModal" x-cloak x-transition
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-data="{
        potongan: 0,
        potonganDisplay: '',
        bayar: 0,
        bayarDisplay: '',
        deskripsi: '',
        layanans: {{ json_encode(
            $layanans->map(function ($l) {
                return [
                    'id' => $l->id,
                    'name' => $l->name,
                    'units' => $l->units->map(function ($u) {
                        return [
                            'unit_satuan' => $u->unit_satuan,
                            'harga' => $u->harga,
                        ];
                    }),
                ];
            }),
        ) }},
        layanansObj: {},
        items: [{ id_layanan: '', unit_satuan: '', qty: 0 }],
    
        addItem() {
            this.items.push({ id_layanan: '', unit_satuan: '', qty: 0 });
        },
    
        removeItem(index) {
            if (this.items.length <= 1) return;
            this.items.splice(index, 1);
        },
    
        formatRp(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        },
    
        getHarga(id, unit) {
            const layanan = this.layanansObj[id];
            if (!layanan) return 0;
    
            const u = layanan.units.find(x => x.unit_satuan === unit);
            return u ? Number(u.harga) : 0;
        },
    
        get subtotal() {
            return this.items.reduce((sum, row) => {
                const qty = Number(row.qty || 0);
                const harga = this.getHarga(row.id_layanan, row.unit_satuan);
    
                if (!harga || qty <= 0) return sum;
    
                return sum + (harga * qty);
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
    
        get kembalian() {
            let k = Number(this.bayar || 0) - this.total;
            return k > 0 ? k : 0;
        },
    
        get minBayar() {
            return Math.ceil(this.total * 0.5); // Minimal 50% pembayaran
        },
    
        get isBayarValid() {
            return this.bayar >= this.minBayar;
        },
    
        onPotonganInput(raw) {
            const cleaned = String(raw).replace(/[^0-9]/g, '');
            let num = cleaned === '' ? 0 : parseInt(cleaned, 10);
            const max = this.subtotal || 0;
            if (num > max) num = max;
            this.potongan = num;
            this.potonganDisplay = num ? num.toLocaleString('id-ID') : '';
        },
    
        onBayarInput(raw) {
            const cleaned = String(raw).replace(/[^0-9]/g, '');
            let num = cleaned === '' ? 0 : parseInt(cleaned, 10);
            this.bayar = num;
            this.bayarDisplay = num ? num.toLocaleString('id-ID') : '';
        },
    
        getUnit(row) {
            return row.unit_satuan || '';
        },
    
        getUnitLabel(row) {
            const unit = this.getUnit(row);
            if (unit === 'kg') return 'Berat (Kg)';
            if (unit === 'pcs') return 'Jumlah (Pcs)';
            if (unit === 'meter') return 'Panjang (Meter)';
            return 'Qty';
        },
    
        getPlaceholder(row) {
            const unit = this.getUnit(row);
            if (unit === 'kg') return 'Contoh: 2.5';
            if (unit === 'pcs') return 'Contoh: 3';
            if (unit === 'meter') return 'Contoh: 5';
            return 'Masukkan jumlah';
        },
    
        getStep(row) {
            if (row.unit_satuan === 'pcs') return 1;
            return 0.1;
        },
    
        isDisabled(row) {
            return !row.id_layanan || !row.unit_satuan;
        },
    
        sanitizeQty(row) {
            if (row.unit_satuan === 'pcs') {
                row.qty = Math.floor(row.qty || 0);
            } else {
                row.qty = Number(parseFloat(row.qty || 0).toFixed(1));
            }
        },
    
        init() {
            this.layanansObj = Object.fromEntries(this.layanans.map(l => [String(l.id), l]));
        },
    }">

    <!-- BACKDROP -->
    <div @click="openAddModal = false" class="absolute inset-0"></div>

    <div @click.stop class="bg-white p-4 rounded-xl w-full max-w-lg shadow-lg relative max-h-[90vh] flex flex-col z-10">

        <button @click="openAddModal = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>

        <h2 class="text-2xl font-bold mb-6 text-center flex-shrink-0">Tambah Transaksi Baru</h2>

        <div class="overflow-y-auto flex-grow pr-2">
            <form method="POST" action="{{ route('transaksi.store') }}">
                @csrf

                <!-- PELANGGAN SELECT -->
                <div class="relative mb-4" x-data="{
                    open: false,
                    search: '',
                    selectedId: '{{ old('id_pelanggan') }}',
                    selectedText: '{{ old('id_pelanggan') ? $pelanggans->firstWhere('id', old('id_pelanggan'))?->name . ' - ' . $pelanggans->firstWhere('id', old('id_pelanggan'))?->kontak : '' }}',
                    pelanggans: {{ json_encode($pelanggans->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'kontak' => $p->kontak, 'text' => $p->name . ' - ' . $p->kontak])) }}
                }" @click.away="open = false">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Pelanggan</label>

                    <div class="relative">
                        <input type="text" x-model="search" @focus="open = true" @input="open = search.length > 0"
                            :placeholder="selectedText || 'Ketik untuk mencari pelanggan...'"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('id_pelanggan')
border-red-500
@enderror"
                            autocomplete="off">
                        <button type="button" x-show="selectedId"
                            @click="selectedId = ''; selectedText = ''; search = ''; open = false"
                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-xl">
                            x
                        </button>
                    </div>

                    <input type="hidden" name="id_pelanggan" :value="selectedId" required>

                    <div x-show="open && search.length > 0" x-cloak
                        class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto shadow-lg">
                        <template
                            x-for="pelanggan in pelanggans.filter(p => p.text.toLowerCase().includes(search.toLowerCase()))"
                            :key="pelanggan.id">
                            <div @click="selectedId = pelanggan.id; selectedText = pelanggan.text; search = pelanggan.text; open = false"
                                class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                                :class="{ 'bg-blue-100': selectedId == pelanggan.id }">
                                <div class="font-medium text-gray-800" x-text="pelanggan.name"></div>
                                <div class="text-sm text-gray-500" x-text="pelanggan.kontak"></div>
                            </div>
                        </template>

                        <div x-show="pelanggans.filter(p => p.text.toLowerCase().includes(search.toLowerCase())).length === 0"
                            class="px-4 py-3 text-gray-500 text-center text-sm">
                            Tidak ada pelanggan ditemukan
                        </div>
                    </div>

                    <div x-show="selectedId && !open" class="mt-1 text-sm text-green-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span x-text="selectedText"></span>
                    </div>

                    @error('id_pelanggan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi (Keyword)</label>
                    <input type="text" x-model="deskripsi" name="deskripsi" value="{{ old('deskripsi') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('deskripsi') border-red-500 @enderror"
                        placeholder="Contoh: Baju Pesta, Selimut Tebal">
                    @error('deskripsi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-gray-700 text-lg font-semibold">Layanan</label>
                        <button type="button" @click="addItem()"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <span>+</span> Tambah Layanan
                        </button>
                    </div>

                    <template x-for="(row, index) in items" :key="index">
                        <div class="mb-4 p-4 rounded-xl border border-gray-200 bg-gray-50 space-y-3">

                            <!-- ROW 1 -->
                            <div class="grid grid-cols-2 gap-3">

                                <!-- LAYANAN -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-semibold mb-1">Layanan</label>
                                    <select :name="`items[${index}][id_layanan]`" x-model="row.id_layanan"
                                        @change="row.unit_satuan = ''"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white" required>
                                        <option value="">Pilih Layanan</option>
                                        <template x-for="layanan in layanans" :key="layanan.id">
                                            <option :value="layanan.id" x-text="layanan.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- UNIT -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-semibold mb-1">Unit</label>
                                    <select :name="`items[${index}][unit_satuan]`" x-model="row.unit_satuan"
                                        :disabled="!row.id_layanan"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white disabled:bg-gray-200"
                                        required>
                                        <option value="">Pilih Unit</option>
                                        <template x-for="unit in (layanansObj[row.id_layanan]?.units || [])">
                                            <option :value="unit.unit_satuan"
                                                x-text="unit.unit_satuan + ' (Rp ' + new Intl.NumberFormat('id-ID').format(unit.harga) + ')'">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- ROW 2 -->
                            <div class="grid grid-cols-2 gap-3 items-end">

                                <!-- QTY -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-semibold mb-1"
                                        x-text="row.id_layanan ? getUnitLabel(row) : 'Qty'">
                                    </label>
                                    <div class="relative">
                                        <input type="number" :step="getStep(row)" :disabled="isDisabled(row)"
                                            @input="sanitizeQty(row)" min="0" :name="`items[${index}][qty]`"
                                            x-model.number="row.qty" :placeholder="getPlaceholder(row)"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 disabled:bg-gray-200"
                                            required>

                                        <span class="absolute right-3 top-2 text-gray-500 text-sm"
                                            x-text="getUnit(row)">
                                        </span>
                                    </div>
                                </div>

                                <!-- HARGA + DELETE -->
                                <div class="flex items-end justify-between">
                                    <div class="text-lg font-semibold text-blue-600">
                                        <span
                                            x-text="formatRp(getHarga(row.id_layanan, row.unit_satuan) * (Number(row.qty || 0)))"></span>
                                    </div>

                                    <button type="button" @click="removeItem(index)"
                                        class="w-9 h-9 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition font-bold"
                                        :class="{ 'opacity-50 cursor-not-allowed': items.length <= 1 }"
                                        :disabled="items.length <= 1">
                                        ✕
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>

                    @error('items')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-lg font-semibold mb-2">Potongan (Rp)</label>
                        <input type="hidden" name="potongan" :value="potongan">
                        <input type="text" x-model="potonganDisplay" @input="onPotonganInput($event.target.value)"
                            inputmode="numeric"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar</label>
                        <input type="hidden" name="jumlah_bayar" :value="bayar">
                        <input type="text" x-model="bayarDisplay" @input="onBayarInput($event.target.value)"
                            inputmode="numeric"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            placeholder="0">
                        <small class="text-gray-500 text-xs mt-1 block">
                            Minimal pembayaran: <span x-text="formatRp(minBayar)"></span> (50% dari total)
                        </small>
                        <p x-show="!isBayarValid && bayar > 0"
                           class="text-red-600 text-sm mt-1">
                           Pembayaran minimal harus 50% dari total harga
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg space-y-2 mb-6 border border-gray-200">
                    <div class="flex justify-between text-md text-gray-600">
                        <span>Subtotal:</span>
                        <span class="font-medium" x-text="formatRp(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-md text-gray-600">
                        <span>Potongan:</span>
                        <span class="font-medium text-red-600" x-text="'- ' + formatRp(potongan)"></span>
                    </div>
                    <hr class="border-gray-300">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total Harga:</span>
                        <span class="text-blue-600" x-text="formatRp(total)"></span>
                    </div>
                    <div class="flex justify-between font-medium text-md"
                        :class="{ 'text-red-600': sisa > 0, 'text-green-600': sisa <= 0 }">
                        <span>Sisa Bayar:</span>
                        <span x-text="formatRp(sisa)"></span>
                    </div>
                    <div class="flex justify-between font-medium text-md text-green-600" x-show="kembalian > 0"
                        x-transition>
                        <span>Kembalian:</span>
                        <span x-text="formatRp(kembalian)"></span>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pb-1">
                    <button type="button" @click="openAddModal = false"
                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" :disabled="!isBayarValid"
                        :class="{ 'opacity-50 cursor-not-allowed': !isBayarValid }"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
