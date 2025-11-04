{{-- 
    Komponen Modal Notifikasi (Alert)
    Dikelola oleh Alpine.js dari layout utama.
--}}
<div x-show="showAlert" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">

    {{-- PERBAIKAN: Menghapus x-show="showAlert" dari div ini --}}
    <div @click.away="showAlert = false" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white p-8 rounded-xl w-full max-w-sm shadow-lg text-center">
        
        <!-- Ikon Dinamis (Success/Error) -->
        <div class="mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center" 
             :class="alertType === 'success' ? 'bg-green-100' : 'bg-red-100'">
            
            <!-- Ikon Success -->
            <svg x-show="alertType === 'success'" class="w-10 h-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <!-- Ikon Error -->
            <svg x-show="alertType === 'error'" class="w-10 h-10 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>

        <!-- Judul Dinamis -->
        <h2 class="text-2xl font-bold mb-2" x-text="alertTitle"></h2>
        
        <!-- Pesan Dinamis -->
        <p class="text-gray-600 mb-6" x-text="alertMessage"></p>

        <!-- Tombol OK -->
        <button type="button" @click="showAlert = false"
            class="px-8 py-2 rounded-lg text-white font-semibold w-full transition-colors"
            :class="alertType === 'success' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'">
            OK
        </button>
    </div>
</div>
