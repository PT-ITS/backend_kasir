<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\CatatanStock;
use App\Models\BiayaOperasional;
use App\Models\ActivityManager;
use Carbon\Carbon;

class LaporanKeuanganController extends Controller
{
    public function laporanKeuanganByToko(Request $request)
    {
        $tokoId = $request->input('toko_id');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        // Validasi input
        if (!$tokoId || !$tahun || !$bulan) {
            return response()->json([
                'id' => '0',
                'message' => 'Parameter toko_id, tahun, dan bulan wajib diisi.'
            ], 400);
        }

        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Laporan Keuangan',
                'deskripsi' => 'Manager melihat laporan keluangan per toko',
            ]);
        }

        // Rentang tanggal lengkap (dengan waktu)
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->startOfDay();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->endOfDay();

        // 1. Data Operasional
        $operasional = BiayaOperasional::where('fk_id_toko', $tokoId)
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => Carbon::parse($item->tanggal_bayar)->toDateString(),
                    'keterangan' => $item->nama_operasional,
                    'kredit' => (int) $item->jumlah_biaya,
                ];
            });

        // Total pengeluaran operasional
        $totalOperasional = $operasional->sum('kredit');

        // 2. Data Transaksi: Kelompokkan berdasarkan tanggal
        $transaksi = Transaksi::where('fk_id_toko', $tokoId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->toDateString();
            })
            ->map(function ($items, $tanggal) {
                return [
                    'tanggal' => $tanggal,
                    'keterangan' => 'Pendapatan Harian',
                    'debit' => $items->sum('total_bayar') -  $items->sum('total_modal'),
                    'kredit' => 0,
                ];
            })
            ->values();

        // Total pemasukan
        $totalPemasukan = $transaksi->sum('debit');

        // 3. Data Belanja Barang
        $belanja = CatatanStock::where('fk_id_toko', $tokoId)
            ->whereBetween('tanggal_belanja', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => Carbon::parse($item->tanggal_belanja)->toDateString(),
                    'keterangan' => 'Belanja Barang',
                    'debit' => 0,
                    'kredit' => (int) $item->total_harga
                ];
            });

        // Total belanja
        $totalBelanja = $belanja->sum('kredit');

        // Gabungkan semua transaksi keuangan dan urutkan berdasarkan tanggal
        $keuangan = $transaksi->merge($belanja)->sortBy('tanggal')->values();

        // Hitung total pengeluaran (operasional + belanja)
        $totalPengeluaran = $totalBelanja + $totalOperasional;

        // Hitung keuntungan
        $keuntungan = $totalPemasukan - $totalPengeluaran;

        // Response final
        return response()->json([
            'id' => '1',
            'data' => [
                'operasional' => $operasional,
                'keuangan' => $keuangan,
                'ringkasan' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_pengeluaran' => $totalPengeluaran,
                    'total_belanja' => $totalBelanja,
                    'keuntungan' => $keuntungan
                ]
            ]
        ]);
    }
}
