<!-- Modal Overlay -->
<div x-show="openAddModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <!-- Modal Content -->
    <div @click.away="openAddModal = false" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="bg-white p-8 rounded-xl w-full max-w-xl shadow-lg relative">

        <button @click="openAddModal = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center">Tambah Layanan Baru</h2>

        <form method="POST" action="{{ route('layanan.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Nama Layanan</label>
                <input type="text" name="name" placeholder="Masukkan nama layanan" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Harga</label>
                <input type="number" name="harga" placeholder="Contoh: 50000" value="{{ old('harga') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('harga') border-red-500 @enderror">
                @error('harga')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Masukkan deskripsi singkat layanan"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Gambar</label>
                <input type="file" name="gambar[]" multiple
                    class="w-full border border-gray-300 rounded-lg p-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('gambar.*') border-red-500 @enderror">
                <small class="text-gray-500">Anda bisa memilih lebih dari satu gambar.</small>
                @error('gambar.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" @click="openAddModal = false"
                    class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Batal</button>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>