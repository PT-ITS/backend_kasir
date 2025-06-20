<?php

namespace App\Http\Controllers;

use App\Models\CatatanStock;
use App\Models\TambahStock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function belanjaStock(Request $request)
    {
        $validated = $request->validate([
            'total_harga' => 'required|string',
            'bukti_nota' => 'required|string',
            'tanggal_belanja' => 'required|date',
            'fk_id_toko' => 'required|exists:tokos,id',
            'details' => 'required|array|min:1',
            'details.*.fk_id_product' => 'required|exists:products,id',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.harga_beli' => 'required|integer|min:1',
            'details.*.expired' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Simpan catatan stok utama
            $catatanStock = CatatanStock::create([
                'total_harga' => $validated['total_harga'],
                'bukti_nota' => $validated['bukti_nota'],
                'tanggal_belanja' => $validated['tanggal_belanja'],
                'fk_id_toko' => $validated['fk_id_toko'],
            ]);

            // Simpan semua detail tambah stok
            foreach ($validated['details'] as $item) {

                Product::where('id', $item['fk_id_product'])->update([
                    'harga_pokok' => $item['harga_beli']
                ]);

                TambahStock::create([
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'expired' => $item['expired'],
                    'fk_id_catatan_stock' => $catatanStock->id,
                    'fk_id_product' => $item['fk_id_product'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully',
                'catatan_stock_id' => $catatanStock->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stock',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listCatatanStock($id)
    {
        $stocks = TambahStock::with(['product', 'catatanStock'])
                    ->where('fk_id_product', $id)
                    ->get();

        $result = $stocks->map(function ($item) {
            return [
                'tanggal' => optional($item->catatanStock)->tanggal ?? '-',
                'product' => optional($item->product)->nama ?? '-',
                'jumlah' => $item->jumlah,
                'harga_beli' => $item->harga_beli,
            ];
        });

        return response()->json([
            'id' => '1',
            'data' => $result,
        ]);
    }
}
