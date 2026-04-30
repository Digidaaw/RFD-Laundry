<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalOrder = Transaksi::count();
        $totalSales = Transaksi::sum('total_harga');
        $orderPending = Transaksi::where('sisa_bayar', '>', 0)->sum('sisa_bayar');

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6);

        $dailyRanges = collect(range(0, 6))->map(fn ($offset) => $startDate->copy()->addDays($offset));

        $dailyLabels = $dailyRanges->map(fn ($date) => $date->format('d M'))->toArray();

        $dailyTotals = Transaksi::whereBetween('tanggal_order', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(tanggal_order) as order_date, SUM(total_harga) as total_sales, SUM(sisa_bayar) as total_piutang')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        $dailyRevenueData = $dailyRanges->map(fn ($date) => $dailyTotals->get($date->toDateString())->total_sales ?? 0)->toArray();
        $dailyDebtData = $dailyRanges->map(fn ($date) => $dailyTotals->get($date->toDateString())->total_piutang ?? 0)->toArray();

        return view('shared.dashboard', compact(
            'totalUser',
            'totalOrder',
            'totalSales',
            'orderPending',
            'dailyLabels',
            'dailyRevenueData',
            'dailyDebtData'
        ));
    }
}
