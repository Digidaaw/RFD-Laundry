<!-- Modal Overlay -->
<div x-show="openModal" x-cloak x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <!-- Modal Content -->
  <div @click.away="openModal = false" x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95" class="bg-white p-8 rounded-xl w-full max-w-xl shadow-lg relative">
    <!-- Close Button -->
    <button @click="openModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-black text-2xl font-bold">
      &times;
    </button>

    <!-- Modal Title -->
    <h2 class="text-2xl font-bold mb-6 text-center">Add Customer</h2>

    <!-- Modal Form -->
    <form>
      <!-- Name Field -->
      <div class="mb-4">
        <label class="block text-gray-700 text-lg font-semibold mb-2">Name</label>
        <input type="text" placeholder="Masukkan nama"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>

      <!-- Contact Field -->
      <div class="mb-4">
        <label class="block text-gray-700 text-lg font-semibold mb-2">Kontak</label>
        <input type="text" placeholder="Masukkan kontak"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
      </div>

      <!-- Buttons -->
      <div class="flex justify-end gap-4 mt-6">
        <button type="button" @click="openModal = false"
          class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
          Cancel
        </button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Saves
        </button>
      </div>
    </form>
  </div>
</div>