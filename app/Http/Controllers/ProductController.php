<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Toko;
use App\Models\CatatanStock;
use App\Models\Transaksi;
use App\Models\TransaksiItem;

class ProductController extends Controller
{
    public function listProductByToko($id)
    {
        $idToko = Toko::where('id', $id)->first();
        $products = Product::where('fk_id_toko', $id)->get();
        return response()->json([
            'id' => '1',
            'data' => $products
        ]);
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

}
