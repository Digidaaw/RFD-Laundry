@extends('layout.admin.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-[1920px] h-[1311px] overflow-hidden relative">
            <!-- Sidebar -->
            <aside class="absolute top-0 left-0 w-[347px] h-full bg-white">
                <div class="relative w-full h-full p-6">
                    <!-- Logo -->
                    <div class="flex items-center gap-4 mb-12">
                        <img src="{{ asset('assets/logo.png') }}" class="w-[56px] h-[56px]" alt="Logo">
                        <h1 class="text-3xl pl-2 font-semibold text-[#151d48]">RFD</h1>
                    </div>


                    <!-- Menu -->
                    <nav class="flex flex-col gap-4">
                        <a href="#" class="flex items-center gap-4 bg-primary-900 text-white px-6 py-4 rounded-xl shadow ">
                            <!-- location photo public > assets -->
                            <img src="{{ asset('assets/dashboard.png') }}" class="w-8 h-8" alt="Dashboard">
                            <span class="text-lg font-semibold">Dashboard</span>
                        </a>

                        <a href="/customer" class="flex items-center gap-4 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl">
                            <img src="{{ asset('assets/customer.png') }}" class="w-6 h-6" alt="Customer">
                            <span class="text-lg">Customer</span>
                        </a>

                        <a href="#" class="flex items-center gap-4 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl">
                            <img src="{{ asset('assets/order.png') }}" class="w-6 h-6" alt="Order">
                            <span class="text-lg">Order</span>
                        </a>

                        <a href="#" class="flex items-center gap-4 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl">
                            <img src="{{ asset('assets/products.png') }}" class="w-6 h-6" alt="Product">
                            <span class="text-lg">Product</span>
                        </a>

                        <a href="#" class="flex items-center gap-4 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl">
                            <img src="{{ asset('assets/report.png') }}" class="w-6 h-6" alt="User">
                            <span class="text-lg">User</span>
                        </a>

                        <a href="#" class="flex items-center gap-4 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl">
                            <img src="{{ asset('assets/kasir.png') }}" class="w-6 h-6" alt="Kasir">
                            <span class="text-lg">Kasir</span>
                        </a>

                        <a href="#" class="flex items-center gap-4 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl">
                            <img src="{{ asset('assets/signout.png') }}" class="w-6 h-6 " alt="Sign Out">
                            <span class="text-lg">Sign Out</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Topbar -->
            <header
                class="absolute top-0 left-[347px] w-[1573px] h-[120px] bg-white flex justify-between items-center px-12">
                <h2 class="text-4xl font-semibold text-[#151d48]">Sales Overview</h2>
                <div class="flex items-center gap-4">
                    <img src="{{ asset('img/rectangle-1393.png') }}" class="w-[60px] h-[60px] rounded-full object-cover"
                        alt="Profile">
                    <div>
                        <p class="text-base font-medium text-gray-900">Maulana</p>
                        <p class="text-sm text-gray-500">Admin</p>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="absolute top-[160px] left-[384px] w-[1472px]">


                <!-- Cards -->
                <div class="grid grid-cols-4 gap-8">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-gray-700 text-sm font-semibold mb-2">Total User</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">1,254</h3>
                        <p class="text-sm text-green-500 font-semibold">↑ 5.4% from yesterday</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-gray-700 text-sm font-semibold mb-2">Total Order</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">893</h3>
                        <p class="text-sm text-green-500 font-semibold">↑ 8.2% from last week</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-gray-700 text-sm font-semibold mb-2">Total Sales</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Rp 87.550.000</h3>
                        <p class="text-sm text-red-500 font-semibold">↓ 2.1% from yesterday</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-gray-700 text-sm font-semibold mb-2">Order Pending</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">31</h3>
                        <p class="text-sm text-yellow-500 font-semibold">~ stable</p>
                    </div>
                </div>

                <!-- Chart placeholder -->
                <div
                    class="mt-12 w-full h-[400px] bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm">
                    [ Chart Component Placeholder ]
                </div>
            </main>
        </div>
    </div>
@endsection