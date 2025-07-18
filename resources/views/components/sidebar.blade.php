@php
    $activePage = Request::segment(1);
@endphp

<aside class="absolute top-0 left-0 w-[347px] h-full bg-white shadow-lg">
    <div class="relative w-full h-full p-6">
        <!-- Logo -->
        <div class="flex items-center gap-4 mb-12 mt-4">
            <img src="{{ asset('assets/logo.png') }}" class="w-[56px] h-[56px]" alt="Logo">
            <h1 class="text-3xl pl-2 font-semibold text-[#151d48]">RFD</h1>
        </div>

        <!-- Menu -->
        <nav class="flex flex-col gap-4">
            <a href="/dashboard"
                class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $activePage === 'dashboard' ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/dashboard.png') }}"
                    class="w-6 h-6 {{ $activePage === 'dashboard' ? 'filter brightness-0 invert' : '' }}"
                    alt="Dashboard">

                <span class="text-lg font-semibold">Dashboard</span>
            </a>


            <a href="/pelanggan"
                class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $activePage === 'pelanggan' ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/customer.svg') }}"
                    class="w-6 h-6 {{ $activePage === 'pelanggan' ? 'filter brightness-0 invert' : '' }}"
                    alt="Dashboard">

                <span class="text-lg font-semibold">Customer</span>
            </a>


            <a href="/transaksi"
                class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $activePage === 'transaksi' ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/order.png') }}"
                    class="w-6 h-6 {{ $activePage === 'transaksi' ? 'filter brightness-0 invert' : '' }}" alt="Dashboard">

                <span class="text-lg font-semibold">Transaksi</span>
            </a>


            <a href="/layanan"
                class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $activePage === 'layanan' ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/layanan.png') }}"
                    class="w-6 h-6 {{ $activePage === 'layanan' ? 'filter brightness-0 invert' : '' }}" alt="Dashboard">

                <span class="text-lg font-semibold">Layanan</span>
            </a>


            <a href="/report"
                class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $activePage === 'report' ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/report.png') }}"
                    class="w-6 h-6 {{ $activePage === 'report' ? 'filter brightness-0 invert' : '' }}" alt="Dashboard">

                <span class="text-lg font-semibold">Report</span>
            </a>


            <a href="/kasir"
                class="flex items-center gap-6 px-6 py-4 rounded-xl font-poppins {{ $activePage === 'kasir' ? 'bg-[#4379EE] text-white' : 'text-[#737791] hover:bg-gray-100' }}">

                <img src="{{ asset('assets/kasir.png') }}"
                    class="w-6 h-6 {{ $activePage === 'kasir' ? 'filter brightness-0 invert' : '' }}" alt="Dashboard">

                <span class="text-lg font-semibold">Kasir</span>
            </a>


            <a href="#" @click.prevent="$dispatch('open-signout-modal')"
                class="flex items-center gap-4 px-8 py-4 text-lg font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Sign Out</span>
            </a>
        </nav>
    </div>
</aside>