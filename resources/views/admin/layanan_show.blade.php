@extends('layout.admin.layout')
@section('title', 'Detail Layanan')

@section('content')
    <div>
        <header class="w-full h-[120px] sticky top-0 z-50 bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Detail Layanan</h2>
            </div>
        </header>

        <main class="p-6 lg:p-10">
            <div class="bg-white rounded-xl shadow-md p-6 max-w-2xl">
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Nama Layanan</p>
                    <p class="text-xl font-semibold text-gray-800">{{ $layanan->name }}</p>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-500">Deskripsi</p>
                    <p class="text-gray-700">{{ $layanan->deskripsi }}</p>
                </div>

                @if($layanan->units->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">Satuan & Harga</p>
                        <table class="w-full text-left border rounded-lg overflow-hidden">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Satuan</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($layanan->units as $unit)
                                    <tr class="border-t">
                                        <td class="p-3 text-gray-800">{{ strtoupper($unit->unit_satuan) }}</td>
                                        <td class="p-3 text-gray-800">Rp {{ number_format($unit->harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('layanan.index') }}"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold">
                        &larr; Kembali
                    </a>
                </div>
            </div>
        </main>
    </div>
@endsection
