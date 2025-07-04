<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\ActivityManager;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function listTransaksiByToko($id)
    {
        $transaksi = Transaksi::where('fk_id_toko', $id)->get();
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Transaksi',
                'deskripsi' => 'Manager melihat list transaksi di toko',
            ]);
        }
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

        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Transaksi',
                'deskripsi' => 'Manager melihat detail transaksi di toko',
            ]);
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

    public function deleteTransaksi($id)
    {
        DB::beginTransaction();

        try {
            $transaksi = Transaksi::find($id);

            if (!$transaksi) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }

            // Ambil semua item dalam transaksi
            $items = TransaksiItem::where('fk_id_transaksi', $id)->get();

            // Kembalikan stok produk
            foreach ($items as $item) {
                $product = Product::find($item->fk_id_product);
                if ($product) {
                    $product->stock_product += $item->jumlah_product;
                    $product->save();
                }
            }

            // Hapus transaksi item
            TransaksiItem::where('fk_id_transaksi', $id)->delete();

            // Hapus transaksi
            $transaksi->delete();

            DB::commit();

            return response()->json([
                'id' => '1',
                'message' => 'Transaksi dan stok berhasil dihapus dan dikembalikan.',
                'data' => $transaksi
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
