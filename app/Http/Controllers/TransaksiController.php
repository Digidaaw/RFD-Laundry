<?php

namespace App\Http\Controllers;


use App\Http\Requests\TransaksiBayarPiutangRequest;
use App\Http\Requests\TransaksiStoreRequest;
use App\Http\Requests\TransaksiUpdateRequest;
use App\Exports\LaporanPelangganExport;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\LayananUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $sort = $request->input('sort', 'updated_latest');
        $type = $request->input('type', 'all');

        $query = Transaksi::with(['pelanggan', 'layanan', 'user', 'items.layanan']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_invoice', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%')
                    ->orWhereHas('pelanggan', function ($pelangganQuery) use ($search) {
                        $pelangganQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('kontak', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($type === 'lunas') {
            $query->where('status_pembayaran', 'Lunas');
        } elseif ($type === 'dp') {
            $query->where('status_pembayaran', 'DP');
        }

        if ($sort === 'updated_oldest') {
            $query->orderBy('updated_at', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        return view('shared.transaksi', [
            'transaksis' => $query->paginate(10)->appends(['search' => $search, 'sort' => $sort, 'type' => $type]),
            'pelanggans' => Pelanggan::orderBy('name')->get(),
            'layanans' => Layanan::orderBy('name')->get(),
            'search' => $search,
            'sort' => $sort,
            'type' => $type,
        ]);
    }

    public function store(TransaksiStoreRequest $request)
    {
        $isMulti = $request->has('items');
        $potongan = (float) ($request->input('potongan') ?? 0);

        $items = $this->normalizeItems($request, $isMulti);

        if ($isMulti) {
            $this->validateLayananUnits($items);
        }

        [$createItems, $subtotalSum] = $this->buildCreateItems($items, $isMulti);

        if ($potongan > $subtotalSum) {
            return back()->withInput()->withErrors(['potongan' => 'Potongan melebihi subtotal.']);
        }

        $totalHarga = max(0, $subtotalSum - $potongan);
        $jumlahBayar = (float) $request->jumlah_bayar;
        $sisaBayar = max(0, $totalHarga - $jumlahBayar);

        $minBayar = (int) ceil($totalHarga * 0.5);
        if ($jumlahBayar < $minBayar) {
            return back()->withInput()->withErrors(['jumlah_bayar' => 'Pembayaran minimal harus 50% dari total harga.']);
        }

        return DB::transaction(function () use ($request, $createItems, $subtotalSum, $totalHarga, $potongan, $jumlahBayar, $sisaBayar) {
            $transaksi = Transaksi::create([
                'id_user' => Auth::id(),
                'created_by' => Auth::user()->username,
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $createItems[0]['layanan_id'] ?? null,
                'deskripsi' => $request->deskripsi,
                'tanggal_order' => now(),
                'subtotal' => $subtotalSum,
                'potongan' => $potongan,
                'total_harga' => $totalHarga,
                'jumlah_bayar' => $jumlahBayar,
                'sisa_bayar' => $sisaBayar,
                'status_pembayaran' => ($sisaBayar <= 0) ? 'Lunas' : 'DP',
            ]);

            $transaksi->items()->createMany($createItems);

            $transaksi->no_invoice = 'IJ' . now()->format('dmY') . str_pad($transaksi->id, 4, '0', STR_PAD_LEFT);
            $transaksi->save();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
        });
    }

    private function normalizeItems(TransaksiStoreRequest $request, bool $isMulti): array
    {
        if ($isMulti) {
            return collect($request->items)
                ->map(fn($item) => [
                    'id_layanan' => (int) $item['id_layanan'],
                    'unit_satuan' => $item['unit_satuan'],
                    'qty' => (float) $item['qty'],
                ])
                ->toArray();
        }

        return [[
            'id_layanan' => (int) $request->id_layanan,
            'qty' => (float) $request->qty,
        ]];
    }

    private function validateLayananUnits(array $items): void
    {
        $unitMap = $this->loadLayananUnitMap($items);

        foreach ($items as $index => $row) {
            $key = "{$row['id_layanan']}-{$row['unit_satuan']}";
            $layananUnit = $unitMap[$key] ?? null;

            if (!$layananUnit) {
                throw ValidationException::withMessages([
                    "items.$index.unit_satuan" => 'Unit tidak tersedia untuk layanan ini.',
                ]);
            }

            if ($layananUnit->unit_satuan === 'pcs') {
                $qty = $row['qty'];

                if ($qty < 1 || (int) $qty != $qty) {
                    throw ValidationException::withMessages([
                        "items.$index.qty" => 'Qty harus bilangan bulat untuk satuan pcs',
                    ]);
                }
            }
        }
    }

    private function loadLayananUnitMap(array $items): array
    {
        $unitIds = collect($items)
            ->unique(fn($item) => $item['id_layanan'] . '-' . $item['unit_satuan']);

        $map = [];
        foreach ($unitIds as $row) {
            $key = "{$row['id_layanan']}-{$row['unit_satuan']}";
            $map[$key] = LayananUnit::where('layanan_id', $row['id_layanan'])
                ->where('unit_satuan', $row['unit_satuan'])
                ->first();
        }

        return $map;
    }

    private function buildCreateItems(array $items, bool $isMulti): array
    {
        $items = collect($items)
            ->filter(fn ($row) => $row['qty'] > 0)
            ->values();

        $layananUnitMap = $isMulti ? $this->loadLayananUnitMap($items->toArray()) : [];
        $layananMap = !$isMulti ? Layanan::whereIn('id', $items->pluck('id_layanan')->unique())
            ->get()
            ->keyBy('id') : null;

        $createItems = $items
            ->map(fn ($row) => $this->buildCreateItem($row, $isMulti, $layananUnitMap, $layananMap))
            ->filter()
            ->values()
            ->toArray();

        if (empty($createItems)) {
            throw ValidationException::withMessages(['items' => 'Minimal 1 layanan harus diinput.']);
        }

        $subtotalSum = array_sum(array_column($createItems, 'subtotal'));

        return [$createItems, $subtotalSum];
    }

    private function buildCreateItem(array $row, bool $isMulti, array $layananUnitMap, $layananMap): ?array
    {
        $harga = $this->resolveItemPrice($row, $isMulti, $layananUnitMap, $layananMap);

        if ($harga === null) {
            return null;
        }

        return $this->formatCreateItem($row, $isMulti, $harga);
    }

    private function resolveItemPrice(array $row, bool $isMulti, array $layananUnitMap, $layananMap): ?float
    {
        if ($isMulti) {
            $key = "{$row['id_layanan']}-{$row['unit_satuan']}";
            $layananUnit = $layananUnitMap[$key] ?? null;

            return $layananUnit ? (float) $layananUnit->harga : null;
        }

        $layanan = $layananMap[$row['id_layanan']] ?? null;

        return $layanan ? (float) $layanan->harga : null;
    }

    private function formatCreateItem(array $row, bool $isMulti, float $harga): array
    {
        $subtotal = $harga * $row['qty'];

        $createItem = [
            'layanan_id' => $row['id_layanan'],
            'qty' => $row['qty'],
            'harga_satuan' => $harga,
            'subtotal' => $subtotal,
        ];

        if ($isMulti) {
            $createItem['unit_satuan'] = $row['unit_satuan'];
        }

        return $createItem;
    }

    public function update(TransaksiUpdateRequest $request, Transaksi $transaksi)
    {
        // Jika ada transaksi_id di request, gunakan itu (dari edit modal)
        if ($request->has('transaksi_id')) {
            $transaksi = Transaksi::findOrFail($request->transaksi_id);
        }

        if ($request->jumlah_bayar > $transaksi->total_harga) {
            return back()->withInput()->withErrors(['jumlah_bayar' => 'Jumlah bayar melebihi total.']);
        }

        $minBayar = (int) ceil($transaksi->total_harga * 0.5);
        if ($request->jumlah_bayar < $minBayar) {
            return back()->withInput()->withErrors(['jumlah_bayar' => 'Pembayaran minimal harus 50% dari total harga.']);
        }

        $sisaBayar = max(0, $transaksi->total_harga - $request->jumlah_bayar);

        $transaksi->update([
            'id_pelanggan' => $request->id_pelanggan,
            'tanggal_order' => $request->tanggal_order,
            'deskripsi' => $request->deskripsi,
            'jumlah_bayar' => $request->jumlah_bayar,
            'sisa_bayar' => $sisaBayar,
            'status_pembayaran' => ($sisaBayar <= 0) ? 'Lunas' : 'DP',
        ]);

        // Handle redirect based on _redirect_url
        if ($request->has('_redirect_url') && $request->_redirect_url) {
            return redirect($request->_redirect_url)->with('success', 'Transaksi berhasil diperbarui.');
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function bayarPiutang(TransaksiBayarPiutangRequest $request, Transaksi $transaksi)
    {
        $jumlahBayarBaru = $transaksi->jumlah_bayar + $request->bayar_sekarang;
        $sisaBayarBaru = max(0, $transaksi->total_harga - $jumlahBayarBaru);

        $transaksi->update([
            'jumlah_bayar' => $jumlahBayarBaru,
            'sisa_bayar' => $sisaBayarBaru,
            'status_pembayaran' => ($sisaBayarBaru <= 0) ? 'Lunas' : 'DP',
        ]);

        return redirect()->route('report.piutang')->with('success', 'Pembayaran berhasil.');
    }
}
