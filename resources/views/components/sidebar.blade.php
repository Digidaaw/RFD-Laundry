@php
    // Variabel ini akan kita gunakan untuk logika 'active'
    $isDashboard = request()->routeIs('dashboard');
    $isPelanggan = request()->routeIs('pelanggan.index');
    $isTransaksi = request()->routeIs('transaksi.index');
    $isLayanan = request()->routeIs('layanan.index');
    $isReport = request()->routeIs('report.*');
    $isKasir = request()->routeIs('users.index');
@endphp
{{-- 
    PERBAIKAN FUNGSIONALITAS:
    - Kelas 'fixed' agar bisa melayang di mobile
    - 'lg:static' untuk kembali normal di desktop
    - 'transform -translate-x-full' untuk sembunyi di mobile
    - 'x-bind:class' untuk logika buka/tutup
--}}
<aside class="fixed top-0 left-0 w-[347px] h-full bg-white shadow-lg z-40
               lg:static lg:translate-x-0
               transform -translate-x-full transition-transform duration-300 ease-in-out"
       x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <div class="relative w-full h-screen p-6 flex flex-col">
        <div class="flex items-center justify-between mb-12 mt-4 flex-shrink-0">
            <div class="flex items-center gap-4">
                <img src="{{ asset('assets/logo.png') }}" class="w-[56px] h-[56px]" alt="Logo">
                <h1 class="text-3xl pl-2 font-semibold text-[#151d48]">RFD</h1>
            </div>
            <button @click="sidebarOpen = false" class=":hidden p-2 text-gray-500 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex flex-col gap-4 flex-grow overflow-y-auto">
            
            {{-- PERBAIKAN LOGIKA: Menggunakan route() dan variabel $is... --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $isDashboard ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/dashboard.png') }}"
                     class="w-6 h-6 {{ $isDashboard ? 'filter brightness-0 invert' : '' }}"
                     alt="Dashboard">
                <span class="text-lg font-semibold">Dashboard</span>
            </a>

            <a href="{{ route('pelanggan.index') }}"
               class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $isPelanggan ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/customer.svg') }}"
                     class="w-6 h-6 {{ $isPelanggan ? 'filter brightness-0 invert' : '' }}"
                     alt="Customer">
                <span class="text-lg font-semibold">Customer</span>
            </a>

            <a href="{{ route('transaksi.index') }}"
               class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $isTransaksi ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/order.png') }}"
                     class="w-6 h-6 {{ $isTransaksi ? 'filter brightness-0 invert' : '' }}" alt="Transaksi">
                <span class="text-lg font-semibold">Transaksi</span>
            </a>

            <a href="{{ route('layanan.index') }}"
               class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $isLayanan ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/layanan.png') }}"
                     class="w-6 h-6 {{ $isLayanan ? 'filter brightness-0 invert' : '' }}" alt="Layanan">
                <span class="text-lg font-semibold">Layanan</span>
            </a>

            <a href="{{ route('report.index') }}"
               class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $isReport ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/report.png') }}"
                     class="w-6 h-6 {{ $isReport ? 'filter brightness-0 invert' : '' }}" alt="Report">
                <span class="text-lg font-semibold">Report</span>
            </a>

            @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $isKasir ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/kasir.png') }}"
                     class="w-6 h-6 {{ $isKasir ? 'filter brightness-0 invert' : '' }}" alt="Kasir">
                <span class="text-lg font-semibold">Kasir</span>
            </a>
            @endif
        </nav>

        <div class="mt-4 flex-shrink-0">
            <a href="#" @click.prevent="$dispatch('open-signout-modal')"
               class="flex items-center gap-6 px-6 py-4 text-lg font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Sign Out</span>
            </a>
        </div>
    </div>
</aside>