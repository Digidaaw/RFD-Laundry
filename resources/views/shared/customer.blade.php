@extends('layout.admin.layout')
@section('title', 'Customer')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@section('content')
    <div class="bg-[#fafbfc] flex justify-center w-full min-h-screen">
        <div class="bg-[#fafbfc] border border-gray-300 w-[1920px] h-[1311px] overflow-hidden relative">

            <!-- Sidebar -->
            @include('components.sidebar')


            <!-- Main Area -->
            <div class="ml-[347px]">
                <!-- Topbar -->
                <header class="w-full h-[120px] bg-white flex justify-between items-center px-12 shadow-sm">
                    <h2 class="text-4xl font-semibold text-[#151d48]">Customer</h2>
                    <div class="flex items-center gap-8">

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
                <main class="p-10" x-data="{ openModal: false }">
                    <div class="flex justify-between items-center mb-8">
                        <div >
                            <!-- Tombol Trigger -->
                            <button @click="openModal = true"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md mb-6">
                                Add New Customer
                            </button>

                            <!-- Include Modal -->
                            @include('components.modal.add-customer')
                        </div>

                        <div class="flex items-center space-x-2 text-gray-700 font-bold text-lg ">
                            <img src="{{ asset('assets/filter.svg') }}" class="fas fa-filter"></i>
                            <span class="pl-2">Filter By</span>
                            <div class="relative">
                                <input type="text" placeholder="Search here..."
                                    class="bg-gray-100 rounded-full py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 w-80">
                                <img src="{{ asset('assets/search-icon.svg') }}" class=" pr-2 fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
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