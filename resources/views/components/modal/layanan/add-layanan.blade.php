<div x-data="{
    units: [{ unit_satuan: '', harga: 0 }],
    addUnit() {
        this.units.push({ unit_satuan: '', harga: 0 });
    },
    removeUnit(index) {
        if (this.units.length <= 1) return;
        this.units.splice(index, 1);
    }
}">
    <div x-show="openAddModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div @click.away="openAddModal = false" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white p-8 rounded-xl w-full max-w-xl shadow-lg relative max-h-screen overflow-y-auto">

            <button @click="openAddModal = false"
                class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
            <h2 class="text-2xl font-bold mb-6 text-center">Tambah Layanan Baru</h2>

            <form method="POST" action="{{ route('layanan.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- NAMA -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Nama Layanan</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-4 py-2 @error('name') border-red-500 @enderror">
                </div>

                <!-- 🔥 MULTI UNIT -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-lg font-semibold text-gray-700">Satuan & Harga</label>
                        <button type="button" @click="addUnit()" class="text-blue-600 text-sm font-semibold">+
                            Tambah</button>
                    </div>

                    <template x-for="(row, index) in units" :key="index">
                        <div class="grid grid-cols-12 gap-3 mb-3 border p-3 rounded-lg">

                            <!-- UNIT -->
                            <div class="col-span-5">
                                <select :name="`units[${index}][unit_satuan]`" x-model="row.unit_satuan"
                                    class="w-full border rounded-lg px-3 py-2">
                                    <option value="">Pilih</option>
                                    <option value="kg">Kg</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="meter">Meter</option>
                                </select>
                            </div>

                            <!-- HARGA -->
                            <div class="col-span-5">
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                    <input type="number" :name="`units[${index}][harga]`" x-model="row.harga"
                                        class="w-full border rounded-lg pl-10 pr-2 py-2" placeholder="50000">
                                </div>
                            </div>

                            <!-- HAPUS -->
                            <div class="col-span-2 flex items-center justify-end">
                                <button type="button" @click="removeUnit(index)" class="text-red-600 font-bold"
                                    :disabled="units.length <= 1">
                                    ✕
                                </button>
                            </div>

                        </div>
                    </template>

                    @error('units')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DESKRIPSI -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- GAMBAR -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Gambar</label>
                    <input type="file" name="gambar[]" multiple class="w-full border rounded-lg p-2">
                </div>

                <!-- BUTTON -->
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" @click="openAddModal = false"
                        class="px-4 py-2 border rounded-lg">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
