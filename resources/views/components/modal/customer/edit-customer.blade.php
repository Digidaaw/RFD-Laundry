<div x-data="{
        open: false,
        data: {} // Data default
     }"
     {{-- Mendengarkan event yang dikirim oleh tombol Update --}}
     @open-edit-customer-modal.window="
        data = $event.detail; 
        open = true;
     "
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    <div @click.away="open = false" x-transition
        class="bg-white p-8 rounded-xl w-full max-w-xl shadow-lg relative">

        <button @click="open = false"
            class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center">Edit Pelanggan</h2>

        <form x-bind:action="data.url" method="POST">
            @csrf
            @method('PUT') 

            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Nama Pelanggan</label>
                <input type="text" name="name" x-model="data.name" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror" />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-lg font-semibold mb-2">Kontak</label>
                <input type="text" name="kontak" x-model="data.kontak" required 
                    inputmode="numeric" pattern="[0-9]*" minlength="10" maxlength="13"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('kontak') border-red-500 @enderror" />
                <p class="text-gray-500 text-sm mt-1">Minimal 10 digit, maksimal 13 digit</p>
                @error('kontak')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-4 mt-6">
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