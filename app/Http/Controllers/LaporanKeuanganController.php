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

        // Filter tanggal awal dan akhir bulan
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // 1. Data Operasional
        $operasional = BiayaOperasional::where('fk_id_toko', $tokoId)
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal_bayar,
                    'keterangan' => $item->nama_operasional,
                    'kredit' => (int) $item->jumlah_biaya,
                ];
            });

        // 2. Data Keuangan
        $transaksi = Transaksi::where('fk_id_toko', $tokoId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->created_at->toDateString(),
                    'keterangan' => 'Pendapatan Harian',
                    'debit' => (int) $item->total_bayar,
                    'kredit' => 0
                ];
            });

        $belanja = CatatanStock::where('fk_id_toko', $tokoId)
            ->whereBetween('tanggal_belanja', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->tanggal_belanja,
                    'keterangan' => 'Belanja Barang',
                    'debit' => 0,
                    'kredit' => (int) $item->total_harga
                ];
            });

        $keuangan = $transaksi->merge($belanja)->sortBy('tanggal')->values();

        $data = [
            'operasional' => $operasional,
            'keuangan' => $keuangan
        ];
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }
}
