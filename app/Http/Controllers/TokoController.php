<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Product;
use App\Models\CatatanStock;
use App\Models\TambahStock;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class TokoController extends Controller
{
    public function list()
    {
        try {
            $datas = Toko::all();
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $datas
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function listByManager($id)
    {
        try {
            $datas = Toko::where('fk_id_manager', $id)->get();
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $datas
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function jumlahProduk()
    {
        $tokos = Toko::all();
        $results = [];

        foreach ($tokos as $toko) {
            $jumlahProduk = Product::where('fk_id_toko', $toko->id)->count();

            $results[] = [
                'id' => $toko->id,
                'nama_toko' => $toko->nama_toko,
                'jumlah_produk' => $jumlahProduk
            ];
        }

        return response()->json($results);
    }

    public function transaksiPerToko()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        $results = [];

        $tokos = Toko::all();

        foreach ($tokos as $toko) {
            $transaksiHariIni = Transaksi::with('items')
                ->where('fk_id_toko', $toko->id)
                ->whereDate('created_at', $today)
                ->get();

            $transaksiIds = Transaksi::where('fk_id_toko', $toko->id)->pluck('id');

            // Jumlah produk terjual
            $hariIni = TransaksiItem::whereIn('fk_id_transaksi', $transaksiIds)
                ->whereDate('created_at', $today)
                ->sum('jumlah_product');

            $mingguIni = TransaksiItem::whereIn('fk_id_transaksi', $transaksiIds)
                ->whereDate('created_at', '>=', $startOfWeek)
                ->sum('jumlah_product');

            $bulanIni = TransaksiItem::whereIn('fk_id_transaksi', $transaksiIds)
                ->whereDate('created_at', '>=', $startOfMonth)
                ->sum('jumlah_product');

            $tahunIni = TransaksiItem::whereIn('fk_id_transaksi', $transaksiIds)
                ->whereDate('created_at', '>=', $startOfYear)
                ->sum('jumlah_product');

            // Keuntungan hari ini
            $keuntunganHariIni = 0;

            foreach ($transaksiHariIni as $trx) {
                foreach ($trx->items as $item) {
                    $productId = $item->fk_id_product;
                    $avgHargaBeli = TambahStock::where('fk_id_product', $productId)->avg('harga_beli') ?? 0;
                    $keuntunganPerItem = ($item->harga_jual_product - $avgHargaBeli) * $item->jumlah_product;
                    $keuntunganHariIni += $keuntunganPerItem;
                }
            }

            // Total belanja (restock) hari ini
            $totalBelanjaHariIni = CatatanStock::where('fk_id_toko', $toko->id)
                ->whereDate('tanggal_belanja', $today)
                ->sum('total_harga');

            // === Pendapatan per hari dari Senin - Minggu ===
            $pendapatanHarian = [];

            for ($i = 0; $i < 7; $i++) {
                $day = Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays($i);
                $transaksiHarian = Transaksi::with('items')
                    ->where('fk_id_toko', $toko->id)
                    ->whereDate('created_at', $day)
                    ->get();

                $pendapatanHari = 0;

                foreach ($transaksiHarian as $trx) {
                    foreach ($trx->items as $item) {
                        $productId = $item->fk_id_product;
                        $avgHargaBeli = TambahStock::where('fk_id_product', $productId)->avg('harga_beli') ?? 0;
                        $keuntunganPerItem = ($item->harga_jual_product - $avgHargaBeli) * $item->jumlah_product;
                        $pendapatanHari += $keuntunganPerItem;
                    }
                }

                $pendapatanHarian[] = round($pendapatanHari);
            }

            $results[] = [
                'id_toko' => $toko->id,
                'nama_toko' => $toko->nama_toko,
                'jumlah_terjual_hari_ini' => $hariIni,
                'jumlah_terjual_minggu_ini' => $mingguIni,
                'jumlah_terjual_bulan_ini' => $bulanIni,
                'jumlah_terjual_tahun_ini' => $tahunIni,
                'keuntungan_hari_ini' => round($keuntunganHariIni),
                'total_belanja_hari_ini' => (int) $totalBelanjaHariIni,
                'pendapatan_harian' => $pendapatanHarian, // <-- array 7 elemen
            ];
        }

        return response()->json([
            'id' => '1',
            'message' => 'data found',
            'data' => $results
        ]);
    }

    public function transaksiByToko($id)
    {
        $today = Carbon::today();

        $toko = Toko::findOrFail($id);

        // Get today's transactions for this toko
        $transaksiHariIni = Transaksi::with(['items' => function ($query) {
            $query->with('product'); // include product info in items
        }])
            ->where('fk_id_toko', $toko->id)
            ->whereDate('created_at', $today)
            ->get();

        $transaksiIds = $transaksiHariIni->pluck('id');

        // Total item quantity sold today
        $jumlahProdukTerjual = TransaksiItem::whereIn('fk_id_transaksi', $transaksiIds)
            ->whereDate('created_at', $today)
            ->sum('jumlah_product');

        // Total pendapatan (total_bayar)
        $totalPendapatan = $transaksiHariIni->sum('total_bayar');

        // Count how many transactions today
        $jumlahTransaksiHariIni = $transaksiHariIni->count();

        // Keuntungan hari ini
        $keuntunganHariIni = 0;

        foreach ($transaksiHariIni as $trx) {
            foreach ($trx->items as $item) {
                $productId = $item->fk_id_product;
                $avgHargaBeli = TambahStock::where('fk_id_product', $productId)->avg('harga_beli') ?? 0;
                $keuntunganPerItem = ($item->harga_jual_product - $avgHargaBeli) * $item->jumlah_product;
                $keuntunganHariIni += $keuntunganPerItem;
            }
        }

        // Format list_transaksi with jumlah item per transaksi
        $formattedTransaksi = $transaksiHariIni->map(function ($trx) {
            $totalItem = $trx->items->sum('jumlah_product');
            return [
                'id' => $trx->id,
                'total_bayar' => $trx->total_bayar,
                'total_modal' => $trx->total_modal,
                'jenis_transaksi' => $trx->jenis_transaksi,
                'created_at' => $trx->created_at,
                'jumlah_item' => $totalItem,
            ];
        });

        // Get item list with product names
        $itemsHariIni = TransaksiItem::with('product')
            ->whereIn('fk_id_transaksi', $transaksiIds)
            ->whereDate('created_at', $today)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jumlah_product' => $item->jumlah_product,
                    'harga_jual_product' => $item->harga_jual_product,
                    'nama_product' => optional($item->product)->nama_product,
                    'created_at' => $item->created_at,
                ];
            });

        return response()->json([
            'id_toko' => $toko->id,
            'nama_toko' => $toko->nama_toko,
            'jumlah_terjual_hari_ini' => $jumlahProdukTerjual,
            'jumlah_transaksi_hari_ini' => $jumlahTransaksiHariIni,
            'keuntungan_hari_ini' => round($keuntunganHariIni),
            'pendapatan_hari_ini' => $totalPendapatan,
            'list_transaksi_hari_ini' => $formattedTransaksi,
            'list_item_terjual_hari_ini' => $itemsHariIni,
        ]);
    }

    public function keuntungan()
    {
        $tokoList = Toko::all();
        $results = [];

        foreach ($tokoList as $toko) {
            $monthlyProfit = 0;
            $yearlyProfit = 0;

            // Ambil semua transaksi toko ini
            $transaksi = Transaksi::with('items')
                ->where('fk_id_toko', $toko->id)
                ->get();

            foreach ($transaksi as $trx) {
                foreach ($trx->items as $item) {
                    $productId = $item->fk_id_product;

                    // Rata-rata harga beli produk ini dari TambahStock
                    $avgHargaBeli = TambahStock::where('fk_id_product', $productId)->avg('harga_beli') ?? 0;

                    $profitPerItem = ($item->harga_jual_product - $avgHargaBeli) * $item->jumlah_product;

                    $createdAt = new Carbon($trx->created_at);

                    if ($createdAt->isCurrentMonth()) {
                        $monthlyProfit += $profitPerItem;
                    }

                    if ($createdAt->isCurrentYear()) {
                        $yearlyProfit += $profitPerItem;
                    }
                }
            }

            // Timeline mingguan
            $startOfWeek = Carbon::now()->startOfWeek(); // Senin
            $endOfWeek = Carbon::now()->endOfWeek();     // Minggu
            $weekDays = CarbonPeriod::create($startOfWeek, $endOfWeek);
            $timelineMingguan = [];

            foreach ($weekDays as $day) {
                $jumlahTransaksi = Transaksi::where('fk_id_toko', $toko->id)
                    ->whereDate('created_at', $day->toDateString())
                    ->count();

                $timelineMingguan[] = [
                    'hari' => $day->translatedFormat('l, d F Y'), // contoh: Senin, 12 Januari 2025
                    'jumlah_transaksi' => $jumlahTransaksi
                ];
            }

            $results[] = [
                'nama_toko' => $toko->nama_toko,
                'keuntungan_bulanan' => round($monthlyProfit),
                'keuntungan_tahunan' => round($yearlyProfit),
                'timeline_mingguan' => $timelineMingguan
            ];
        }

        return response()->json($results);
    }

    public function detail($id)
    {
        try {
            $datas = Toko::find($id);
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $datas
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validateData = $request->validate([
                'nama_toko' => 'required',
                'hp_toko' => 'required',
                'alamat_toko' => 'required',
                'fk_id_manager' => 'required|exists:users,id'
            ]);

            $datas = Toko::create([
                'nama_toko' => $validateData['nama_toko'],
                'hp_toko' => $validateData['hp_toko'],
                'alamat_toko' => $validateData['alamat_toko'],
                'fk_id_manager' => $validateData['fk_id_manager'],
            ]);

            return response()->json([
                'id' => '1',
                'message' => 'success',
                'data' => $datas
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'id' => '0',
                'message' => $e->errors(),
                'data' => []
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validateData = $request->validate([
                'nama_toko' => 'required',
                'hp_toko' => 'required',
                'alamat_toko' => 'required',
                'fk_id_manager' => 'required|exists:users,id'
            ]);
            $datas = Toko::find($id);
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            $datas->nama_toko = $validateData['nama_toko'];
            $datas->hp_toko = $validateData['hp_toko'];
            $datas->alamat_toko = $validateData['alamat_toko'];
            $datas->fk_id_manager = $validateData['fk_id_manager'];
            $datas->save();

            return response()->json([
                'id' => '1',
                'message' => 'success',
                'data' => $datas
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'id' => '0',
                'message' => $e->errors(),
                'data' => []
            ]);
        }
    }
    public function delete($id)
    {
        try {
            $datas = Toko::find($id);
            if ($datas) {
                $datas->delete();
                return response()->json([
                    'id' => '1',
                    'message' => 'success',
                    'data' => $datas
                ]);
            } else {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
