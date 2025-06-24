<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\Toko;
use App\Models\BiayaOperasional;


class DashboardController extends Controller
{
    //modal, pengeluaran, pemasukan, laba bersih
    public function laporanSemuaToko(Request $request)
    {
        $filterTahun = $request->query('tahun');

        // Buat closure untuk filter
        $filterScope = function ($query) use ($filterTahun) {
            if ($filterTahun) $query->whereYear('created_at', $filterTahun);
        };

        // Ambil modal, pemasukan, pengeluaran sesuai filter
        $modal = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_modal) as total_modal')
            ->when($filterTahun, fn($q) => $q->whereYear('created_at', $filterTahun))
            ->groupBy('tahun')
            ->pluck('total_modal', 'tahun');

        $pemasukan = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_bayar) as total_pemasukan')
            ->when($filterTahun, fn($q) => $q->whereYear('created_at', $filterTahun))
            ->groupBy('tahun')
            ->pluck('total_pemasukan', 'tahun');

        $pengeluaran = BiayaOperasional::selectRaw('YEAR(created_at) as tahun, SUM(jumlah_biaya) as total_pengeluaran')
            ->when($filterTahun, fn($q) => $q->whereYear('created_at', $filterTahun))
            ->groupBy('tahun')
            ->pluck('total_pengeluaran', 'tahun');

        $laporan = [];

        if ($filterTahun) {
            // Jika filter tahun ada, fokus hanya di tahun itu
            $tahun = intval($filterTahun);
            $laporan[] = [
                'tahun' => $tahun,
                'total_modal' => $modal[$tahun] ?? 0,
                'total_pemasukan' => $pemasukan[$tahun] ?? 0,
                'total_pengeluaran' => $pengeluaran[$tahun] ?? 0,
                'laba_bersih' => ($pemasukan[$tahun] ?? 0) - (($modal[$tahun] ?? 0) + ($pengeluaran[$tahun] ?? 0)),
            ];
        } else {
            // Tanpa filter: tampilkan semua tahun
            $tahunGabungan = collect($modal->keys())
                ->merge($pemasukan->keys())
                ->merge($pengeluaran->keys())
                ->unique()
                ->sort();

            foreach ($tahunGabungan as $tahun) {
                $m = $modal[$tahun] ?? 0;
                $p = $pemasukan[$tahun] ?? 0;
                $k = $pengeluaran[$tahun] ?? 0;
                $laporan[] = [
                    'tahun' => $tahun,
                    'total_modal' => $m,
                    'total_pemasukan' => $p,
                    'total_pengeluaran' => $k,
                    'laba_bersih' => $p - ($m + $k),
                ];
            }
        }

        return response()->json([
            'id' => '1',
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
                    'laba_bersih' => $pemasukanTahun - ($modalTahun + $pengeluaranTahun)
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
