<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Toko;
use App\Models\BiayaOperasional;
use App\Models\TambahStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getYears()
    {
        // Get all distinct years from transaksi
        $availableYears = Transaksi::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            return response()->json([
                'id' => '0',
                'data' => [],
            ]);
        }

        $minYear = min($availableYears);
        $maxYear = max($availableYears);

        // Create a full range: 2 years before min, to 2 years after max
        $allYears = range($minYear - 2, $maxYear + 2);

        return response()->json([
            'id' => '1',
            'data' => $allYears,
        ]);
    }

    //modal, pengeluaran, pemasukan, laba bersih
    public function laporanSemuaToko(Request $request)
    {
        $filterTahun = $request->input('tahun');
        if (!$filterTahun) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter tahun wajib'
            ], 400);
        }

        // Pastikan tahun jadi string
        $tahunStr = (string) $filterTahun;

        // Query modal
        $modal = TambahStock::selectRaw('YEAR(created_at) as tahun, SUM(jumlah * harga_beli) as total_modal')
            ->whereYear('created_at', $tahunStr)
            ->groupBy('tahun')
            ->pluck('total_modal', 'tahun');

        // Query hpp
        $hpp = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_modal) as total_hpp')
            ->whereYear('created_at', $tahunStr)
            ->groupBy('tahun')
            ->pluck('total_hpp', 'tahun');

        // Query pemasukan
        $pemasukan = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_bayar) as total_pemasukan')
            ->whereYear('created_at', $tahunStr)
            ->groupBy('tahun')
            ->pluck('total_pemasukan', 'tahun');

        // Query pengeluaran
        $pengeluaran = BiayaOperasional::selectRaw('YEAR(created_at) as tahun, SUM(jumlah_biaya) as total_pengeluaran')
            ->whereYear('created_at', $tahunStr)
            ->groupBy('tahun')
            ->pluck('total_pengeluaran', 'tahun');

        // Ambil data hanya tahun filter
        $m = $modal[$tahunStr] ?? 0;   // Total modal (stok masuk)
        $hpp = $hpp[$tahunStr] ?? 0;   // Harga pokok penjualan
        $o = $pemasukan[$tahunStr] ?? 0; // Omset
        $p = $o - $hpp;                // Pemasukan bersih (laba kotor)
        $k = $pengeluaran[$tahunStr] ?? 0; // Pengeluaran operasional
        $laba = $p - $k;               // Laba bersih

        $laporan = [
            'tahun' => (int)$tahunStr,
            'total_modal' => $m,
            'total_hpp' => $hpp,
            'total_omset' => $o,
            'total_pemasukan' => $p,
            'total_pengeluaran' => $k,
            'laba_bersih' => $laba,
            'pajak' => 0.12 * max(0, $laba)
        ];

        return response()->json([
            'status' => 'success',
            'data' => $laporan
        ]);
    }

    public function laporanPerToko()
    {
        $tokoList = Toko::all();
        $laporan = [];

        foreach ($tokoList as $toko) {
            $tokoId = $toko->id;

            // Data total keseluruhan
            $totalHpp = Transaksi::where('fk_id_toko', $tokoId)->sum('total_modal');
            $totalPemasukan = Transaksi::where('fk_id_toko', $tokoId)->sum('total_bayar');
            $totalPengeluaran = BiayaOperasional::where('fk_id_toko', $tokoId)->sum('jumlah_biaya');
            $labaBersih = $totalPemasukan - ($totalHpp + $totalPengeluaran);

            // Data bulan ini
            $now = now();
            $perhitunganBulanIniModal = Transaksi::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('total_modal');

            $bulanIniModal = TambahStock::join('products', 'tambah_stocks.fk_id_product', '=', 'products.id')
                ->where('products.fk_id_toko', $tokoId)
                ->whereYear('tambah_stocks.created_at', $now->year)
                ->sum(DB::raw('jumlah * harga_beli'));

            $bulanIniHpp = Transaksi::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('total_modal');

            $hariIniPemasukan = Transaksi::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->whereDay('created_at', $now->day)
                ->sum('total_bayar');

            $bulanIniPemasukan = Transaksi::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('total_bayar');

            $bulanIniPengeluaran = BiayaOperasional::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('jumlah_biaya');

            $labaBersihBulanIni = $bulanIniPemasukan - ($perhitunganBulanIniModal + $bulanIniPengeluaran);

            // Data per tahun (time series laba bersih)
            $transaksiPerTahun = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_modal) as modal, SUM(total_bayar) as pemasukan')
                ->where('fk_id_toko', $tokoId)
                ->groupBy('tahun')
                ->get()
                ->keyBy('tahun');

            $biayaPerTahun = BiayaOperasional::selectRaw('YEAR(created_at) as tahun, SUM(jumlah_biaya) as pengeluaran')
                ->where('fk_id_toko', $tokoId)
                ->groupBy('tahun')
                ->get()
                ->keyBy('tahun');

            $tahunGabungan = $transaksiPerTahun->keys()->merge($biayaPerTahun->keys())->unique();

            $labaPerTahun = [];
            foreach ($tahunGabungan as $tahun) {
                $modalTahun = $transaksiPerTahun[$tahun]->modal ?? 0;
                $pemasukanTahun = $transaksiPerTahun[$tahun]->pemasukan ?? 0;
                $pengeluaranTahun = $biayaPerTahun[$tahun]->pengeluaran ?? 0;

                $labaPerTahun[] = [
                    'tahun' => $tahun,
                    'total_modal' => $modalTahun,
                    'total_pemasukan' => $pemasukanTahun,
                    'total_pengeluaran' => $pengeluaranTahun,
                    'laba_bersih' => $pemasukanTahun - ($modalTahun + $pengeluaranTahun),
                ];
            }

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
                        $hargaPokok = $item->product->harga_pokok ?? 0;
                        $keuntunganPerItem = ($item->harga_jual_product - $hargaPokok) * $item->jumlah_product;
                        $pendapatanHari += $keuntunganPerItem;
                    }
                }

                $pendapatanHarian[] = round($pendapatanHari);
            }

            // Gabungkan ke laporan utama
            $laporan[] = [
                'nama_toko' => $toko->nama_toko,
                'total_modal' => $bulanIniModal,
                'total_hpp' => $bulanIniHpp,
                'total_pemasukan' => $totalPemasukan - $totalHpp,
                'total_pengeluaran' => $totalPengeluaran,
                'omset_hari_ini' => $hariIniPemasukan,
                'omset_bulan_ini' => $bulanIniPemasukan,
                'laba_bersih' => $labaBersih,
                'laba_bersih_bulan_ini' => $labaBersihBulanIni,
                'laba_bersih_per_tahun' => $labaPerTahun,
                'pendapatan_harian' => $pendapatanHarian,
            ];
        }

        return response()->json([
            'id' => '1',
            'data' => $laporan
        ]);
    }
}
