<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Kasir;
use App\Models\CatatanStock;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\TransaksiItem;

class ProductController extends Controller
{
    public function listProductByToko()
    {
        $idToko = Kasir::where('fk_id_user', auth()->user()->id)->first();
        $products = Product::where('fk_id_toko', $idToko->fk_id_toko)->get();
        return response()->json([
            'id' => '1',
            'data' => $products
        ]);
    }

    public function listProductByIdToko($id)
    {
        $products = Product::where('fk_id_toko', $id)->get();
        return response()->json([
            'id' => '1',
            'data' => $products
        ]);
    }

    public function createNewProduct(Request $request)
    {
        try {
            $validateData = $request->validate([
                'nama_product' => 'required',
                'stock_product' => 'required',
                'harga_jual' => 'required',
                'barcode' => 'required',
                'fk_id_toko' => 'required|exists:tokos,id'
            ]);

            $products = Product::create([
                'nama_product' => $validateData['nama_product'],
                'stock_product' => $validateData['stock_product'],
                'harga_jual' => $validateData['harga_jual'],
                'barcode' => $validateData['barcode'],
                'fk_id_toko' => $validateData['fk_id_toko']
            ]);

            return response()->json([
                'id' => '1',
                'data' => 'product berhasil di tambahkan'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'data' => $th->getMessage()
            ]);
        }
    }

    public function detailProduct($id)
    {
        $products = Product::find($id);
        if (!$products) {
            return response()->json([
                'id' => '0',
                'message' => 'data not found'
            ]);
        }
        return response()->json([
            'id' => '1',
            'data' => $products
        ]);
    }

    public function updateHargaProduct(Request $request)
    {
        $validateData = $request->validate([
            'id_product' => 'required',
            'harga_jual' => 'required',
        ]);

        $products = Product::find($validateData['id_product']);

        if (!$products) {
            return response()->json([
                'id' => '0',
                'message' => 'data not found'
            ]);
        }

        $products->harga_jual = $validateData['harga_jual'];
        $products->save();

        return response()->json([
            'id' => '1',
            'data' => 'harga product berhasil di update'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $tokoId = Kasir::where('fk_id_user', auth()->user()->id)->first()->fk_id_toko ?? '';

        if (!$query || !$tokoId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter query dan fk_id_toko dibutuhkan',
            ], 400);
        }

        $results = Product::where('fk_id_toko', $tokoId)
            ->where(function ($q) use ($query) {
                $q->where('nama_product', 'like', '%' . $query . '%')
                    ->orWhere('barcode', 'like', '%' . $query . '%');
            })
            ->get();

        return response()->json([
            'success' => true,
            'count' => $results->count(),
            'data' => $results,
        ]);
    }
}
