@extends('layout.admin.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-[1920px] h-[1311px] overflow-hidden relative">
            
        <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Topbar -->
            <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
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