<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function listTransaksiByToko($id)
    {
        $transaksi = Transaksi::where('fk_id_toko', $id)->get();
        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    public function detailTransaksi($id)
    {
        $transaksi = Transaksi::with('items')->find($id);

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    public function createTransaksi(Request $request)
    {
        $request->validate([
            'no_invoice' => 'required|string',
            'total_bayar' => 'required|numeric',
            'total_modal' => 'required|numeric',
            'jenis_transaksi' => 'required|string',
            'fk_id_kasir' => 'required|integer',
            'fk_id_toko' => 'required|integer',
            'items' => 'required|array',
            'items.*.jumlah_product' => 'required|integer',
            'items.*.harga_jual_product' => 'required|numeric',
            'items.*.fk_id_product' => 'required|integer'
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'no_invoice' => $request->no_invoice,
                'total_bayar' => $request->total_bayar,
                'total_modal' => $request->total_modal,
                'jenis_transaksi' => $request->jenis_transaksi,
                'fk_id_kasir' => $request->fk_id_kasir,
                'fk_id_toko' => $request->fk_id_toko,
            ]);

            foreach ($request->items as $item) {
                // Create transaksi item
                TransaksiItem::create([
                    'fk_id_transaksi' => $transaksi->id,
                    'jumlah_product' => $item['jumlah_product'],
                    'harga_jual_product' => $item['harga_jual_product'],
                    'fk_id_product' => $item['fk_id_product'],
                ]);

                // Kurangi stok produk
                $product = Product::find($item['fk_id_product']);
                if ($product) {
                    $product->stock_product = max(0, (int)$product->stock_product - $item['jumlah_product']);
                    $product->save();
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $datas = Transaksi::find($id);
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
