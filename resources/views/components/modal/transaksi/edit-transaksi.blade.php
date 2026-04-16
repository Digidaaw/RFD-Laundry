<!-- Modal Edit Transaksi (Komponen Mandiri yang Lengkap) -->
<div x-data="{
        open: false,
        data: {}, // Data transaksi akan diisi di sini
        choicesInstance: null, // Untuk menyimpan instance Choices.js
        bayarNumeric: 0,
        bayarDisplay: '',
        bayarError: '',
        
        // Fungsi init() akan dijalankan saat komponen Alpine dimuat
        init() {
            this.$watch('open', (value) => {
                // Jika 'open' berubah menjadi 'true' (modal dibuka)
                if (value) {
                    // $nextTick akan menunggu DOM selesai diperbarui (modalnya terlihat)
                    this.$nextTick(() => {
                        const element = this.$refs.pelangganSelectEdit;
                        if (!element) return; // Pastikan elemennya ada

                        // Hancurkan instance Choices.js lama jika ada (mencegah duplikat)
                        if (this.choicesInstance) {
                            this.choicesInstance.destroy();
                        }

                        // Buat instance Choices.js yang baru
                        this.choicesInstance = new Choices(element, {
                            searchEnabled: true,
                            itemSelectText: 'Pilih',
                            searchFields: ['label'], // Mencari di seluruh teks
                            shouldSort: true, // Memastikan daftar selalu terurut
                            placeholder: true,
                            placeholderValue: 'Ketik nama atau kontak...'
                        });

                        // Set nilai pelanggan yang sedang dipilih
                        if (this.data.id_pelanggan) {
                            // Beri sedikit waktu agar Choices.js selesai render
                            setTimeout(() => {
                                if (this.choicesInstance) {
                                    this.choicesInstance.setChoiceByValue(String(this.data.id_pelanggan));
                                }
                            }, 50); // 50ms biasanya cukup
                        }
                    });
                } else {
                    // Jika modal ditutup, hancurkan instance agar bersih
                    if (this.choicesInstance) {
                        this.choicesInstance.destroy();
                        this.choicesInstance = null;
                    }
                }
            });
        },
        
        // Fungsi ini dipanggil oleh event $dispatch dari tombol 'Update'
        openModal(event) {
            let detail = event.detail;
            
            // Format tanggal agar sesuai dengan input type date (YYYY-MM-DD)
            if (detail.tanggal_order) {
                // Ambil 10 karakter pertama
                detail.tanggal_order = detail.tanggal_order.substring(0, 10);
            }
            this.data = detail;
            this.bayarNumeric = Number(detail.jumlah_bayar || 0);
            this.bayarDisplay = this.bayarNumeric ? this.bayarNumeric.toLocaleString('id-ID') : '';
            this.bayarError = '';
            this.open = true; // Ini akan memicu $watch di atas

            // Set form action menggunakan route yang sudah di-generate oleh backend
            this.$nextTick(() => {
                if (this.$refs.editForm && detail.id) {
                    // Gunakan base URL + id untuk action
                    this.$refs.editForm.action = window.location.origin + '/transaksi/' + detail.id;
                }
            });
        },

        validateBayar() {
            const total = Number(this.data.total_harga || 0);
            const bayar = Number(this.bayarNumeric || 0);
            if (bayar > total) {
                this.bayarError = 'Jumlah bayar tidak boleh melebihi total tagihan.';
            } else {
                this.bayarError = '';
            }
        },

        onBayarInput(raw) {
            const cleaned = String(raw).replace(/[^0-9]/g, '');
            let num = cleaned === '' ? 0 : parseInt(cleaned, 10);
            const max = Number(this.data.total_harga || 0);
            if (num > max) num = max;
            this.bayarNumeric = num;
            this.data.jumlah_bayar = num;
            this.bayarDisplay = num ? num.toLocaleString('id-ID') : '';
            this.validateBayar();
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

    <!-- Konten Modal -->
    <div @click.away="open = false" x-transition
        class="bg-white p-8 rounded-xl w-full max-w-lg shadow-lg relative max-h-[90vh] flex flex-col">
        <button @click="open = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold z-10">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center flex-shrink-0">Update Transaksi</h2>

        <!-- Form dibuat scrollable jika kontennya panjang -->
        <div class="overflow-y-auto flex-grow pr-2">
            
            <form method="POST" action="" x-ref="editForm">
                @csrf
                @method('PUT')

                {{-- Input tersembunyi untuk redirect --}}
                <input type="hidden" name="_redirect_url" :value="window.location.href.includes('report') ? window.location.href : ''">
                <input type="hidden" name="transaksi_id" :value="data.id">

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Tanggal Order</label>
                    <input type="date" name="tanggal_order" x-model="data.tanggal_order"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Pelanggan <span class="text-red-500">*</span></label>
                    @if(isset($pelanggans))
                        <select name="id_pelanggan" x-ref="pelangganSelectEdit" required
                            class="@error('id_pelanggan') border-red-500 @enderror">
                            <option value="" disabled selected>Pilih Pelanggan...</option>
                            @foreach($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}">{{ $pelanggan->name }} - {{ $pelanggan->kontak }}</option>
                            @endforeach
                        </select>
                        @error('id_pelanggan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    @else
                        <p class="text-red-500">Error: Data pelanggan tidak ditemukan.</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi (Keyword)</label>
                    <textarea name="deskripsi" x-model="data.deskripsi" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('deskripsi') border-red-500 @enderror"
                              placeholder="Contoh: Baju Pesta, Selimut Tebal"></textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Jumlah Bayar <span class="text-red-500">*</span></label>
                    <input type="hidden" name="jumlah_bayar" :value="bayarNumeric" required>
                    <input type="text"
                           x-model="bayarDisplay"
                           x-on:input="onBayarInput($event.target.value)"
                           inputmode="numeric" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('jumlah_bayar') border-red-500 @enderror">
                    <small x-show="data.total_harga" class="text-gray-500" x-text="'Total Tagihan: Rp ' + new Intl.NumberFormat('id-ID').format(data.total_harga)"></small>
                    <p x-show="bayarError"
                       x-text="bayarError"
                       class="text-red-600 text-sm mt-1"></p>
                    @error('jumlah_bayar')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
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