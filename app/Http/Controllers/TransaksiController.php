<?php

namespace App\Http\Controllers;


use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\LayananUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function store(Request $request)
{
    $isMulti = $request->has('items');

    if ($isMulti) {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'items' => 'required|array|min:1',
            'items.*.id_layanan' => 'required|exists:layanans,id',
            'items.*.unit_satuan' => 'required|string|in:kg,pcs,meter',
            'items.*.qty' => 'required|numeric|min:0.1|max:999.9',
            'potongan' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:255',
        ], [
            'id_pelanggan.required' => 'Pelanggan harus dipilih.',
            'id_pelanggan.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'items.required' => 'Minimal 1 layanan harus ditambahkan.',
            'items.min' => 'Minimal 1 layanan harus ditambahkan.',
            'items.*.id_layanan.required' => 'Layanan harus dipilih.',
            'items.*.id_layanan.exists' => 'Layanan yang dipilih tidak ditemukan.',
            'items.*.unit_satuan.required' => 'Satuan harus dipilih.',
            'items.*.unit_satuan.in' => 'Satuan hanya boleh: kg, pcs, atau meter.',
            'items.*.qty.required' => 'Qty harus diisi.',
            'items.*.qty.numeric' => 'Qty harus berupa angka.',
            'items.*.qty.min' => 'Qty minimal 0.1.',
            'items.*.qty.max' => 'Qty maksimal 999.9.',
            'potongan.numeric' => 'Potongan harus berupa angka.',
            'potongan.min' => 'Potongan tidak boleh negatif.',
            'jumlah_bayar.required' => 'Jumlah bayar harus diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'jumlah_bayar.min' => 'Jumlah bayar tidak boleh negatif.',
        ]);
    } else {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'id_layanan' => 'required|exists:layanans,id',
            'qty' => 'required|numeric|min:0.1|max:999.9',
            'potongan' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:255',
        ], [
            'id_pelanggan.required' => 'Pelanggan harus dipilih.',
            'id_pelanggan.exists' => 'Pelanggan yang dipilih tidak ditemukan.',
            'id_layanan.required' => 'Layanan harus dipilih.',
            'id_layanan.exists' => 'Layanan yang dipilih tidak ditemukan.',
            'qty.required' => 'Qty harus diisi.',
            'qty.numeric' => 'Qty harus berupa angka.',
            'qty.min' => 'Qty minimal 0.1.',
            'qty.max' => 'Qty maksimal 999.9.',
            'potongan.numeric' => 'Potongan harus berupa angka.',
            'potongan.min' => 'Potongan tidak boleh negatif.',
            'jumlah_bayar.required' => 'Jumlah bayar harus diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'jumlah_bayar.min' => 'Jumlah bayar tidak boleh negatif.',
        ]);
    }

    $potongan = $request->input('potongan') ?? 0;
    $potongan = (float) $potongan;

    return DB::transaction(function () use ($request, $isMulti, $potongan) {
        $items = [];

        // ✅ SIAPKAN ITEMS DULU
        if ($isMulti) {
            foreach ($request->items as $index => $row) {
                $items[] = [
                    'id_layanan' => (int) $row['id_layanan'],
                    'unit_satuan' => $row['unit_satuan'],
                    'qty' => (float) $row['qty'],
                ];
            }
        } else {
            $items[] = [
                'id_layanan' => (int) $request->id_layanan,
                'qty' => (float) $request->qty,
            ];
        }

        // ✅ VALIDASI LAYANAN UNIT SESUAI DENGAN UNIT YANG DIPILIH
        if ($isMulti) {
            foreach ($request->items as $index => $row) {
                $layananUnit = LayananUnit::where('layanan_id', $row['id_layanan'])
                    ->where('unit_satuan', $row['unit_satuan'])
                    ->first();

                if (!$layananUnit) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            "items.$index.unit_satuan" => "Unit tidak tersedia untuk layanan ini."
                        ]);
                }

                // Validasi untuk pcs harus bilangan bulat
                if ($layananUnit->unit_satuan === 'pcs') {
                    $qty = (float) $row['qty'];

                    if ($qty < 1 || (int)$qty != $qty) {
                        return back()
                            ->withInput()
                            ->withErrors([
                                "items.$index.qty" => "Qty harus bilangan bulat untuk satuan pcs"
                            ]);
                    }
                }
            }
        }

        // ✅ DEFINISIKAN LAYANAN+UNIT MAP
        $layananUnitMap = [];
        if ($isMulti) {
            $unitIds = collect($items)->map(fn($i) => [$i['id_layanan'], $i['unit_satuan']])->unique();
            foreach ($unitIds as [$layananId, $unitSatuan]) {
                $key = "$layananId-$unitSatuan";
                $layananUnitMap[$key] = LayananUnit::where('layanan_id', $layananId)
                    ->where('unit_satuan', $unitSatuan)
                    ->first();
            }
        } else {
            // Untuk single item, gunakan harga dari layanan (backward compatibility)
            $layananMap = Layanan::whereIn('id', collect($items)->pluck('id_layanan')->unique())
                ->get()
                ->keyBy('id');
        }

        // ... lanjutkan dengan perhitungan subtotal
        $subtotalSum = 0;
        $createItems = [];

        foreach ($items as $row) {
            $qty = (float) $row['qty'];
            if ($qty <= 0)
                continue;

            if ($isMulti) {
                $key = "{$row['id_layanan']}-{$row['unit_satuan']}";
                $layananUnit = $layananUnitMap[$key] ?? null;

                if (!$layananUnit)
                    continue;

                $harga = (float) $layananUnit->harga;
            } else {
                $layanan = $layananMap[$row['id_layanan']] ?? null;

                if (!$layanan)
                    continue;

                $harga = (float) $layanan->harga;
            }

            $subtotal = $harga * $qty;
            $subtotalSum += $subtotal;

            $createItems[] = [
                'layanan_id' => $row['id_layanan'],
                'qty' => $qty,
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($createItems)) {
            return back()->withInput()->withErrors(['items' => 'Minimal 1 layanan harus diinput.']);
        }

        if ($potongan > $subtotalSum) {
            return back()->withInput()->withErrors(['potongan' => 'Potongan melebihi subtotal.']);
        }

        $totalHarga = max(0, $subtotalSum - $potongan);
        $jumlahBayar = (float) $request->jumlah_bayar;
        $sisaBayar = max(0, $totalHarga - $jumlahBayar);

        // Validasi minimal pembayaran 50%
        $minBayar = ceil($totalHarga * 0.5);
        if ($jumlahBayar < $minBayar) {
            return back()->withInput()->withErrors(['jumlah_bayar' => 'Pembayaran minimal harus 50% dari total harga.']);
        }

        $singleLayananId = $createItems[0]['layanan_id'] ?? null;

        $transaksi = Transaksi::create([
            'id_user' => Auth::id(),
            'created_by' => Auth::user()->username,
            'id_pelanggan' => $request->id_pelanggan,
            'id_layanan' => $singleLayananId,
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

    public function update(Request $request, Transaksi $transaksi)
    {
        // Jika ada transaksi_id di request, gunakan itu (dari edit modal)
        if ($request->has('transaksi_id')) {
            $transaksi = Transaksi::findOrFail($request->transaksi_id);
        }

        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'tanggal_order' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        if ($request->jumlah_bayar > $transaksi->total_harga) {
            return back()->withInput()->withErrors(['jumlah_bayar' => 'Jumlah bayar melebihi total.']);
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

    public function destroy(Request $request, Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function bayarPiutang(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'bayar_sekarang' => 'required|numeric|min:0.01|lte:' . $transaksi->sisa_bayar,
        ]);

        $jumlahBayarBaru = $transaksi->jumlah_bayar + $request->bayar_sekarang;
        $sisaBayarBaru = max(0, $transaksi->total_harga - $jumlahBayarBaru);

        $transaksi->update([
            'jumlah_bayar' => $jumlahBayarBaru,
            'sisa_bayar' => $sisaBayarBaru,
            'status_pembayaran' => ($sisaBayarBaru <= 0) ? 'Lunas' : 'DP',
        ]);

        return redirect()->route('report.piutang')->with('success', 'Pembayaran berhasil.');
    }

    public function exportPelangganExcel($pelangganId)
    {
        $data = Transaksi::where('id_pelanggan', $pelangganId)->with('layanan')->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            'No Invoice',
            'Tanggal',
            'Layanan',
            'Deskripsi',
            'Total Harga',
            'Bayar',
            'Sisa',
            'Status'
        ], null, 'A1');

        $row = 2;
        foreach ($data as $t) {
            $sheet->fromArray([
                $t->no_invoice,
                $t->tanggal_order,
                $t->layanan->name ?? '-',
                $t->deskripsi,
                $t->total_harga,
                $t->jumlah_bayar,
                $t->sisa_bayar,
                $t->status_pembayaran,
            ], null, 'A' . $row++);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'laporan.xlsx');
    }
}