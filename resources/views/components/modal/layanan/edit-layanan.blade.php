<div x-data="{
    open: false,
    data: { gambar: [], unit_satuan: '' }, // Tambahkan unit_satuan di default
    imagesToDelete: [],
    showDeleteConfirm: false,
    imageNameToDelete: null,

    // Fungsi untuk membuka modal dan mengisi data
    initModal(event) {
        this.data = event.detail;
        this.imagesToDelete = [];
        this.showDeleteConfirm = false;
        this.open = true;
    },

    data: {
        gambar: [],
        units: []
    },

    // Fungsi untuk konfirmasi hapus gambar
    confirmRemoveImage(imageName) {
        this.imageNameToDelete = imageName;
        this.showDeleteConfirm = true;
    },

    // Fungsi untuk eksekusi hapus gambar
    executeRemoveImage() {
        if (this.imageNameToDelete) {
            if (!this.imagesToDelete.includes(this.imageNameToDelete)) {
                this.imagesToDelete.push(this.imageNameToDelete);
            }
            this.data.gambar = this.data.gambar.filter(img => img !== this.imageNameToDelete);
        }
        this.showDeleteConfirm = false;
        this.imageNameToDelete = null;
    }
}" @open-edit-modal.window="initModal($event)" @keydown.escape.window="open = false"
    x-show="open" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <div @click.away="open = false" x-transition
        class="bg-white p-8 rounded-xl w-full max-w-2xl shadow-lg relative max-h-[90vh] flex flex-col">
        <button @click="open = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold z-30">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center flex-shrink-0">Edit Layanan</h2>

        <div class="overflow-y-auto flex-grow">
            <form x-bind:action="data.url" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <template x-for="image in imagesToDelete">
                    <input type="hidden" name="images_to_delete[]" :value="image">
                </template>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Nama Layanan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="data.name" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-gray-700 text-lg font-semibold">Satuan & Harga <span class="text-red-500">*</span></label>
                        <button type="button" @click="data.units.push({ unit_satuan: '', harga: 0 })"
                            class="text-sm text-blue-600 font-semibold hover:text-blue-800">
                            + Tambah
                        </button>
                    </div>

                    <template x-for="(unit, index) in data.units" :key="index">
                        <div class="grid grid-cols-12 gap-2 mb-2 items-center border p-2 rounded-lg">

                            <!-- UNIT -->
                            <div class="col-span-5">
                                <select :name="`units[${index}][unit_satuan]`" x-model="unit.unit_satuan" required
                                    class="w-full border rounded-lg px-2 py-1 @error('units.*.unit_satuan') border-red-500 @enderror">
                                    <option value="">Pilih Satuan</option>
                                    <option value="kg">kg</option>
                                    <option value="pcs">pcs</option>
                                    <option value="meter">meter</option>
                                </select>
                            </div>

                            <!-- HARGA -->
                            <div class="col-span-5">
                                <input type="number" :name="`units[${index}][harga]`" x-model="unit.harga" required min="0"
                                    class="w-full border rounded-lg px-2 py-1 @error('units.*.harga') border-red-500 @enderror" placeholder="Harga">
                            </div>

                            <!-- DELETE -->
                            <div class="col-span-2 text-right">
                                <button type="button" @click="data.units.splice(index,1)"
                                    class="text-red-600 font-bold">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    @if ($errors->has('units') || $errors->has('units.*.unit_satuan') || $errors->has('units.*.harga'))
                        <div class="text-red-500 text-sm">
                            @error('units')
                                <p>{{ $message }}</p>
                            @enderror
                            @foreach ($errors->get('units.*.unit_satuan') as $messages)
                                @foreach ($messages as $message)
                                    <p>{{ $message }}</p>
                                @endforeach
                            @endforeach
                            @foreach ($errors->get('units.*.harga') as $messages)
                                @foreach ($messages as $message)
                                    <p>{{ $message }}</p>
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" x-model="data.deskripsi" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                </div>

                <div class="mb-2">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Gambar Saat Ini</label>
                    <div class="overflow-x-auto pb-2">
                        <div class="flex flex-nowrap gap-3">
                            <template x-if="data.gambar && data.gambar.length > 0">
                                <template x-for="image in data.gambar" :key="image">
                                    <div class="relative flex-shrink-0">
                                        <img :src="'/images/layanan/' + image"
                                            class="w-24 h-24 object-cover rounded-md border">
                                        <button type="button" @click="confirmRemoveImage(image)"
                                            class="absolute top-0 right-0 m-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-sm hover:bg-red-700 transition-colors">&times;</button>
                                    </div>
                                </template>
                            </template>
                            <template x-if="!data.gambar || data.gambar.length === 0">
                                <p class="text-gray-500">Tidak ada gambar tersisa.</p>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Upload Gambar Baru</label>
                    <input type="file" name="gambar[]" multiple accept="image/*"
                        class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 @error('gambar') border-red-500 @enderror">
                    <p class="text-gray-500 text-sm mt-1">Format: JPEG, PNG, JPG, GIF, SVG (Max: 2MB per file)</p>
                    @if ($errors->has('gambar') || $errors->has('gambar.*'))
                        <div class="text-red-500 text-sm mt-2">
                            @error('gambar')
                                <p>{{ $message }}</p>
                            @enderror
                            @foreach ($errors->get('gambar.*') as $messages)
                                @foreach ($messages as $message)
                                    <p>{{ $message }}</p>
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-4 mt-6 pt-4 border-t flex-shrink-0">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>

        <div x-show="showDeleteConfirm" x-cloak x-transition
            class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center z-20 rounded-xl">
            <div @click.away="showDeleteConfirm = false" x-transition
                class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center">
                <h3 class="text-xl font-bold mb-4">Hapus Gambar?</h3>
                <p class="text-gray-600 mb-6">Anda yakin ingin menghapus gambar ini?</p>
                <div class="flex justify-center gap-4">
                    <button type="button" @click="showDeleteConfirm = false"
                        class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200">Batal</button>
                    <button type="button" @click="executeRemoveImage()"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
