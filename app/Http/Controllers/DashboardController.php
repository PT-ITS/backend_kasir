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
        // Ambil parameter tahun dari request (misalnya ?tahun=2024)
        $filterTahun = $request->query('tahun');

        // Jika ada filter tahun, tambahkan ke query
        $modalQuery = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_modal) as total_modal')
            ->when($filterTahun, function ($query) use ($filterTahun) {
                $query->whereYear('created_at', $filterTahun);
            })
            ->groupBy('tahun')
            ->pluck('total_modal', 'tahun');

        $pemasukanQuery = Transaksi::selectRaw('YEAR(created_at) as tahun, SUM(total_bayar) as total_pemasukan')
            ->when($filterTahun, function ($query) use ($filterTahun) {
                $query->whereYear('created_at', $filterTahun);
            })
            ->groupBy('tahun')
            ->pluck('total_pemasukan', 'tahun');

        $pengeluaranQuery = BiayaOperasional::selectRaw('YEAR(created_at) as tahun, SUM(jumlah_biaya) as total_pengeluaran')
            ->when($filterTahun, function ($query) use ($filterTahun) {
                $query->whereYear('created_at', $filterTahun);
            })
            ->groupBy('tahun')
            ->pluck('total_pengeluaran', 'tahun');

        // Gabungkan semua tahun yang tersedia dari 3 sumber
        $tahunGabungan = collect($modalQuery->keys())
            ->merge($pemasukanQuery->keys())
            ->merge($pengeluaranQuery->keys())
            ->unique();

        // Buat laporan per tahun
        $laporan = [];

        foreach ($tahunGabungan as $tahun) {
            $m = $modalQuery[$tahun] ?? 0;
            $p = $pemasukanQuery[$tahun] ?? 0;
            $k = $pengeluaranQuery[$tahun] ?? 0;

            $labaBersih = $p - ($m + $k);

            $laporan[] = [
                'tahun' => $tahun,
                'total_modal' => $m,
                'total_pemasukan' => $p,
                'total_pengeluaran' => $k,
                'laba_bersih' => $labaBersih
            ];
        }

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
            'status' => 'success',
            'data' => $laporan
        ]);
    }
}
