<!-- Modal Edit Komponen Mandiri -->
<div x-data="{
        open: false,
        data: { gambar: [] }, // Data default
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
     }"
     @open-edit-modal.window="initModal($event)"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <!-- Konten Modal -->
    <div @click.away="open = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-2xl shadow-lg relative max-h-[90vh] flex flex-col">
        <button @click="open = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold z-10">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center flex-shrink-0">Edit Layanan</h2>

        <div class="overflow-y-auto flex-grow relative">
            <form x-bind:action="data.url" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <template x-for="image in imagesToDelete">
                    <input type="hidden" name="images_to_delete[]" :value="image">
                </template>

                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Nama Layanan</label>
                    <input type="text" name="name" x-model="data.name" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Harga</label>
                    <input type="number" name="harga" x-model="data.harga" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" x-model="data.deskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                </div>
                
                <div class="mb-2">
                    <label class="block text-gray-700 text-lg font-semibold mb-2">Gambar Saat Ini</label>
                    <div class="overflow-x-auto pb-2">
                        <div class="flex flex-nowrap gap-3">
                            <template x-if="data.gambar && data.gambar.length > 0">
                                <template x-for="image in data.gambar" :key="image">
                                    <div class="relative flex-shrink-0">
                                        <img :src="'/images/layanan/' + image" class="w-24 h-24 object-cover rounded-md border">
                                        <button type="button" @click="confirmRemoveImage(image)" class="absolute top-0 right-0 m-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-sm hover:bg-red-700 transition-colors">&times;</button>
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
                    <input type="file" name="gambar[]" multiple class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0">
                </div>

                <div class="flex justify-end gap-4 mt-6 pt-4 border-t flex-shrink-0">
                    <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>

            <div x-show="showDeleteConfirm" x-cloak x-transition class="absolute inset-0 bg-black bg-opacity-10 flex items-center justify-center z-20 rounded-xl">
                <div @click.away="showDeleteConfirm = false" x-transition class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center">
                    <h3 class="text-xl font-bold mb-4">Hapus Gambar?</h3>
                    <p class="text-gray-600 mb-6">Anda yakin ingin menghapus gambar ini?</p>
                    <div class="flex justify-center gap-4">
                        <button type="button" @click="showDeleteConfirm = false" class="px-6 py-2 border rounded-lg text-gray-800 bg-gray-100 hover:bg-gray-200">Batal</button>
                        <button type="button" @click="executeRemoveImage()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
