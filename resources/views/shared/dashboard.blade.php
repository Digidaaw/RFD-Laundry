@extends('layout.admin.layout')
@section('title', 'Dashboard')

@section('content')
    <div>
        <!-- Topbar -->
        <header class="w-full h-[120px] sticky top-0 z-50 bg-white flex justify-between items-center px-6 lg:px-12 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Tombol Hamburger untuk Mobile --}}
                <button @click.stop="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-3xl lg:text-4xl font-semibold text-[#151d48]">Sales Overview</h2>
            </div>

            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <p class="uppercase font-semibold text-sm text-gray-900">{{ Auth::user()->role ?? 'Panel' }}</p>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="p-6 md:p-10">

            <!-- Cards -->
            {{-- Dibuat responsif: 1 kolom di HP, 2 di tablet, 4 di desktop --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">

                <!-- Card Total User (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total User</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $totalUser ?? 0 }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Total akun terdaftar</p>
                </div>

                <!-- Card Total Order (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total Order</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $totalOrder ?? 0 }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Transaksi sepanjang waktu</p>
                </div>

                <!-- Card Total Sales (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total Sales</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}
                    </h3>
                    <p class="text-sm text-gray-500 font-semibold">Total nilai transaksi</p>
                </div>

                <!-- Card Total Piutang (Dinamis) -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-gray-700 text-sm font-semibold mb-2">Total Piutang</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Rp
                        {{ number_format($orderPending ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-sm text-gray-500 font-semibold">Total jumlah yang belum dibayar</p>
                </div>
            </div>

            <!-- Stacked Column Chart: Total Pendapatan Harian dan Piutang Harian -->
            <div class="mt-12 w-full bg-white rounded-xl shadow-md p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Pendapatan & Piutang Mingguan</h3>
                        <p class="text-sm text-gray-500">Total pendapatan harian dan piutang harian dalam 7 hari terakhir.</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        Rentang: {{ now()->subDays(6)->format('d M') }} - {{ now()->format('d M Y') }}
                    </div>
                </div>
                <div class="w-full h-[420px]">
                    <canvas id="weeklyRevenueChart" class="w-full h-full"></canvas>
                </div>
            </div>

        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('weeklyRevenueChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailyLabels) !!},
                    datasets: [
                        {
                            label: 'Pendapatan Harian',
                            data: {!! json_encode($dailyRevenueData) !!},
                            backgroundColor: 'rgba(59, 130, 246, 0.85)',
                            borderColor: 'rgba(37, 99, 235, 1)',
                            borderWidth: 1,
                        },
                        {
                            label: 'Piutang Harian',
                            data: {!! json_encode($dailyDebtData) !!},
                            backgroundColor: 'rgba(34, 197, 94, 0.85)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#1f2937',
                                boxWidth: 12,
                                padding: 16,
                            },
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: '#475569',
                                font: {
                                    size: 12,
                                },
                            },
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                color: '#475569',
                                font: {
                                    size: 12,
                                },
                                callback: function (value) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        maximumFractionDigits: 0,
                                    }).format(value);
                                },
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.2)',
                            },
                        },
                    },
                },
            });
        });
    </script>
@endpush
