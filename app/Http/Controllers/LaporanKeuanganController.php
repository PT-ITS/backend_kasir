<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\CatatanStock;
use App\Models\BiayaOperasional;
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

        // 2. Data Transaksi (pendapatan)
        $transaksi = Transaksi::where('fk_id_toko', $tokoId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => Carbon::parse($item->created_at)->toDateString(),
                    'keterangan' => 'Pendapatan Harian',
                    'debit' => (int) $item->total_bayar,
                    'kredit' => 0
                ];
            });

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

        // Gabung dan urutkan berdasarkan tanggal
        $keuangan = $transaksi->merge($belanja)->sortBy('tanggal')->values();

        // Response final
        return response()->json([
            'id' => '1',
            'data' => [
                'operasional' => $operasional,
                'keuangan' => $keuangan
            ]
        ]);
    }
}
