<div x-show="openAddModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  
  <div @click.away="openAddModal = false" x-transition
    class="bg-white p-8 rounded-xl w-full max-w-md shadow-lg relative">
    <button @click="openAddModal = false"
      class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
    <h2 class="text-2xl font-bold mb-6 text-center">Tambah Pelanggan Baru</h2>
    <form method="POST" action="{{ route('pelanggan.store') }}">
      @csrf
      <div class="mb-4">
        <label class="block text-gray-700 text-lg font-semibold mb-2">Nama Pelanggan</label>
        <input type="text" name="name" placeholder="Masukkan nama pelanggan" value="{{ old('name') }}"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('name') border-red-500 @enderror">
        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-lg font-semibold mb-2">Kontak (No. HP)</label>
        <input type="text" name="kontak" placeholder="Masukkan nomor kontak" value="{{ old('kontak') }}"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('kontak') border-red-500 @enderror">
        @error('kontak')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="flex justify-end gap-4 mt-6">
        <button type="button" @click="openAddModal = false"
          class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">Batal</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
      </div>
    </form>
  </div>
</div>