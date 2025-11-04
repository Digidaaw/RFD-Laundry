<!-- Modal Tambah Transaksi -->
<div x-show="openAddModal" x-cloak x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div @click.away="openAddModal = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-lg shadow-lg relative"
         x-data="{ 
            selectedLayanan: null, 
            berat: 0, 
            bayar: 0,
            total: 0,
            sisa: 0,
            layanans: {{ json_encode($layanans) }},
            updateTotal() {
                const layanan = this.layanans.find(l => l.id == this.selectedLayanan);
                if (layanan && this.berat > 0) {
                    this.total = layanan.harga * this.berat;
                } else {
                    this.total = 0;
                }
                this.updateSisa();
            },
            updateSisa() {
                this.sisa = this.total - this.bayar;
                if (this.sisa < 0) {
                    this.sisa = 0;
                }
            },
            init() {
                // PERBAIKAN: Kita akan menginisialisasi Choices.js saat modal dibuka
                // menggunakan $watch untuk keandalan yang lebih baik.
                this.$watch('openAddModal', (value) => {
                    if (value) {
                        // Menunggu DOM diperbarui sebelum inisialisasi
                        this.$nextTick(() => {
                            const element = this.$refs.pelangganSelect;
                            // Hancurkan instance lama jika ada untuk menghindari duplikasi
                            if (element.choices) {
                                element.choices.destroy();
                            }
                            // Buat instance baru
                            new Choices(element, {
                                searchEnabled: true,
                                itemSelectText: 'Pilih',
                                placeholder: true,
                                placeholderValue: 'Ketik nama atau kontak...',
                                searchFields: ['label'],
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
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Pelanggan</label>
                    {{-- Pastikan elemen ini ada sebelum diinisialisasi --}}
                    <select name="id_pelanggan" x-ref="pelangganSelect">
                        <option value="">Ketik untuk mencari pelanggan...</option>
                        @foreach($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->id }}">{{ $pelanggan->name }} - {{ $pelanggan->kontak }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Layanan</label>
                    <select name="id_layanan" x-model="selectedLayanan" @change="updateTotal" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white">
                        <option value="">Pilih Layanan</option>
                        @foreach($layanans as $layanan)
                            <option value="{{ $layanan->id }}">{{ $layanan->name }} (Rp {{ number_format($layanan->harga, 0, ',', '.') }}/kg)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Berat (kg)</label>
                    <input type="number" step="0.1" name="berat_laundry" x-model="berat" @input.debounce.500ms="updateTotal" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar</label>
                <input type="number" name="jumlah_bayar" x-model="bayar" @input.debounce.500ms="updateSisa" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex justify-between font-bold text-lg">
                    <span>Total Harga:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)"></span>
                </div>
                <div class="flex justify-between font-medium text-md mt-2" :class="{ 'text-red-600': sisa > 0, 'text-green-600': sisa <= 0 }">
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
