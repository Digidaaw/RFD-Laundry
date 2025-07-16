@extends('layout.admin.layout')
@section('title', 'Customer')

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-[1920px] h-[1311px] overflow-hidden relative">
            <!-- Sidebar -->
            <aside class="absolute top-0 left-0 w-[347px] h-full bg-white shadow-lg">
                <div class="p-6">
                    <!-- Logo -->
                    <div class="flex items-center gap-4 mb-12 mt-4">
                        <img src="{{ asset('assets/logo.png') }}" class="w-[56px] h-[56px]" alt="Logo">
                        <h1 class="text-3xl pl-2 font-semibold text-[#151d48]">RFD</h1>
                    </div>

                    <!-- Menu -->
                    <nav class="flex flex-col gap-4">
                        <a href="/dashboard"
                            class="flex items-center gap-6 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl font-poppins">
                            <img src="{{ asset('assets/dashboard.png') }}" class="w-8 h-8" alt="Dashboard">
                            <span class="text-lg font-semibold">Dashboard</span>
                        </a>
                        <a href="/customer"
                            class="flex items-center gap-6 bg-[#4379EE] text-white px-6 py-4 rounded-xl shadow font-poppins">
                            <img src="{{ asset('assets/customer.svg') }}" class=" w-8 h-8 text-white" alt="Customer">
                            <span class="text-lg font-semibold">Customer</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-6 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl font-poppins">
                            <img src="{{ asset('assets/order.png') }}" class="w-8 h-8" alt="Order">
                            <span class="text-lg">Order</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-6 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl font-poppins">
                            <img src="{{ asset('assets/products.png') }}" class="w-8 h-8" alt="Product">
                            <span class="text-lg">Product</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-6 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl font-poppins">
                            <img src="{{ asset('assets/report.png') }}" class="w-8 h-8" alt="User">
                            <span class="text-lg">User</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-6 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl font-poppins">
                            <img src="{{ asset('assets/kasir.png') }}" class="w-8 h-8" alt="Kasir">
                            <span class="text-lg">Kasir</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-6 px-6 py-4 text-[#737791] hover:bg-gray-100 rounded-xl mt-8 font-poppins">
                            <img src="{{ asset('assets/signout.png') }}" class="w-8 h-8" alt="Sign Out">
                            <span class="text-lg">Sign Out</span>
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Area -->
            <div class="ml-[347px]">
                <!-- Topbar -->
                <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                    <h2 class="text-4xl font-semibold text-[#151d48]">Customer</h2>
                    <div class="flex items-center gap-8">
                        <div class="relative">
                            <input type="text" placeholder="Search here..."
                                class="bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-80">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('img/rectangle-1393.png') }}"
                                class="w-[60px] h-[60px] rounded-full object-cover" alt="Profile">
                            <div>
                                <p class="text-base font-medium text-gray-900">Maulana</p>
                                <p class="text-sm text-gray-500">Admin</p>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Content Area -->
                <main class="p-10">
                    <div class="flex justify-between items-center mb-8">
                        <button
                            class="bg-[#4379EE] text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-blue-600 transition">
                            Add New Member
                        </button>
                        <div class="flex items-center space-x-2 text-gray-700 font-bold text-lg">
                            <i class="fas fa-filter"></i>
                            <span>Filter By</span>
                        </div>
                    </div>


                    <!-- Customer Table -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">ID</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">NAME</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700">KONTAK</th>
                                        <th class="p-4 text-lg font-extrabold text-gray-700 text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-4 font-semibold text-gray-600">00001</td>
                                        <td class="p-4 font-semibold text-gray-800">Christine Brooks</td>
                                        <td class="p-4 font-semibold text-gray-600">93478134034</td>
                                        <td class="p-4 flex justify-center items-center space-x-2">
                                            <button
                                                class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">Update</button>
                                            <button
                                                class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-4 font-semibold text-gray-600">00002</td>
                                        <td class="p-4 font-semibold text-gray-800">Rosie Pearson</td>
                                        <td class="p-4 font-semibold text-gray-600">17389182941</td>
                                        <td class="p-4 flex justify-center items-center space-x-2">
                                            <button
                                                class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">Update</button>
                                            <button
                                                class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-4 font-semibold text-gray-600">00003</td>
                                        <td class="p-4 font-semibold text-gray-800">Darrell Caldwell</td>
                                        <td class="p-4 font-semibold text-gray-600">13074348343</td>
                                        <td class="p-4 flex justify-center items-center space-x-2">
                                            <button
                                                class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">Update</button>
                                            <button
                                                class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-4 font-semibold text-gray-600">00004</td>
                                        <td class="p-4 font-semibold text-gray-800">Gilbert Johnston</td>
                                        <td class="p-4 font-semibold text-gray-600">13043048934</td>
                                        <td class="p-4 flex justify-center items-center space-x-2">
                                            <button
                                                class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">Update</button>
                                            <button
                                                class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-4 font-semibold text-gray-600">00005</td>
                                        <td class="p-4 font-semibold text-gray-800">Alan Cain</td>
                                        <td class="p-4 font-semibold text-gray-600">031329983</td>
                                        <td class="p-4 flex justify-center items-center space-x-2">
                                            <button
                                                class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">Update</button>
                                            <button
                                                class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4 font-semibold text-gray-600">00006</td>
                                        <td class="p-4 font-semibold text-gray-800">Alfred Murray</td>
                                        <td class="p-4 font-semibold text-gray-600">07401841842</td>
                                        <td class="p-4 flex justify-center items-center space-x-2">
                                            <button
                                                class="bg-green-100 text-green-700 font-bold py-2 px-6 rounded-md hover:bg-green-200 transition">Update</button>
                                            <button
                                                class="bg-red-100 text-red-700 font-bold py-2 px-6 rounded-md hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection