<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Toko;
use App\Models\BiayaOperasional;
use App\Models\ActivityManager;


class DashboardController extends Controller
{
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
        $modal = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_modal) as total_modal')
            ->whereYear('created_at', $tahunStr)
            ->groupBy('tahun')
            ->pluck('total_modal', 'tahun');

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
        $m = $modal[$tahunStr] ?? 0;
        $p = $pemasukan[$tahunStr] ?? 0;
        $k = $pengeluaran[$tahunStr] ?? 0;
        $laba = $p - ($m + $k);

        $laporan = [
            'tahun' => (int)$tahunStr,
            'total_modal' => $m,
            'total_pemasukan' => $p,
            'total_pengeluaran' => $k,
            'laba_bersih' => $laba,
            'pajak' => 0.005 * max(0, $p - ($m + $k))
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
            $totalModal = Transaksi::where('fk_id_toko', $tokoId)->sum('total_modal');
            $totalPemasukan = Transaksi::where('fk_id_toko', $tokoId)->sum('total_bayar');
            $totalPengeluaran = BiayaOperasional::where('fk_id_toko', $tokoId)->sum('jumlah_biaya');
            $labaBersih = $totalPemasukan - ($totalModal + $totalPengeluaran);

            // Data bulan ini
            $now = now();
            $bulanIniModal = Transaksi::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('total_modal');

            $bulanIniPemasukan = Transaksi::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('total_bayar');

            $bulanIniPengeluaran = BiayaOperasional::where('fk_id_toko', $tokoId)
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('jumlah_biaya');

            $labaBersihBulanIni = $bulanIniPemasukan - ($bulanIniModal + $bulanIniPengeluaran);

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

            // Gabungkan ke laporan utama
            $laporan[] = [
                'nama_toko' => $toko->nama_toko,
                'total_modal' => $totalModal,
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'laba_bersih' => $labaBersih,
                'laba_bersih_bulan_ini' => $labaBersihBulanIni,
                'laba_bersih_per_tahun' => $labaPerTahun
            ];
        }

        return response()->json([
            'id' => '1',
            'data' => $laporan
        ]);
    }
}
