<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Kasir;
use App\Models\User;
use App\Models\CatatanStock;
use App\Models\TambahStock;
use App\Models\Toko;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Carbon\Carbon;

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
        $today = Carbon::today();

        $toko = Toko::find($id);

        // Ambil semua produk berdasarkan ID toko
        $products = Product::where('fk_id_toko', $id)->get();

        // Hitung jumlah total produk
        $jumlahProduk = $products->count();

        // Hitung produk dengan stok 0
        $produkStokHabis = $products->where('stock_product', 0)->count();

        // Ambil semua ID produk untuk pengecekan expired
        $productIds = $products->pluck('id');

        // Hitung produk yang memiliki stok expired
        $jumlahProdukExpired = TambahStock::whereIn('fk_id_product', $productIds)
            ->whereDate('expired', '<', $today)
            ->count();

        return response()->json([
            'id' => $id,
            'nama_toko' => $toko->nama_toko,
            'jumlah_produk' => $jumlahProduk,
            'jumlah_produk_stok_0' => $produkStokHabis,
            'jumlah_produk_expired' => $jumlahProdukExpired,
            'data' => $products
        ]);
    }

    public function listProductByBarcode($barcode)
    {
        try {
            $idToko = Kasir::where('fk_id_user', auth()->user()->id)->first();
            $products = Product::where('fk_id_toko', $idToko->fk_id_toko)
                ->where('barcode', $barcode)
                ->first();
            if (!$products) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $products
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function listProductByNama(Request $request)
    {
        try {
            $nama = $request->query('nama_product');
            if (!$nama) {
                return response()->json([
                    'id' => '0',
                    'message' => 'Parameter nama_product wajib diisi.',
                    'data' => []
                ]);
            }

            $idToko = Kasir::where('fk_id_user', auth()->user()->id)->first();
            $product = Product::where('fk_id_toko', $idToko->fk_id_toko)
                ->where('nama_product', 'like', '%' . $nama . '%')
                ->get();
            if (!$product) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $product
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function createNewProduct(Request $request)
    {
        try {
            $validateData = $request->validate([
                'kode_product' => 'required',
                'nama_product' => 'required',
                'stock_product' => 'required',
                'harga_jual' => 'required',
                'harga_pokok' => 'required',
                'barcode' => 'required',
                'satuan' => 'required',
                'jenis' => 'required',
                'merek' => 'required',
                'fk_id_toko' => 'required|exists:tokos,id'
            ]);

            $products = Product::create([
                'kode_product' => $validateData['kode_product'],
                'nama_product' => $validateData['nama_product'],
                'stock_product' => $validateData['stock_product'],
                'harga_jual' => $validateData['harga_jual'],
                'harga_pokok' => $validateData['harga_pokok'],
                'barcode' => $validateData['barcode'],
                'satuan' => $validateData['satuan'],
                'jenis' => $validateData['jenis'],
                'merek' => $validateData['merek'],
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
            'harga_pokok' => 'required',
            'satuan' => 'required',
            'jenis' => 'required',
            'merek' => 'required',
        ]);

        $levelUser = User::find(auth()->user()->id)->level ?? '';

        if ($levelUser != '1') {
            return response()->json([
                'id' => '0',
                'data' => 'anda tidak memiliki akses untuk aksi ini'
            ]);
        }

        $products = Product::find($validateData['id_product']);

        if (!$products) {
            return response()->json([
                'id' => '0',
                'message' => 'data not found'
            ]);
        }

        $products->harga_jual = $validateData['harga_jual'];
        $products->harga_pokok = $validateData['harga_pokok'];
        $products->satuan = $validateData['satuan'];
        $products->jenis = $validateData['jenis'];
        $products->merek = $validateData['merek'];
        $products->save();

        return response()->json([
            'id' => '1',
            'data' => 'harga product berhasil di update'
        ]);
    }

    public function deleteProduct(Request $request)
    {
        try {
            $validateData = $request->validate([
                'id_product' => 'required'
            ]);

            $levelUser = User::find(auth()->user()->id)->level ?? '';

            if ($levelUser != '1') {
                return response()->json([
                    'id' => '0',
                    'data' => 'anda tidak memiliki akses untuk aksi ini'
                ]);
            }


            Product::find($validateData['id_product'])->delete();
            return response()->json([
                'id' => '1',
                'data' => 'product berhasil di delete'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'data' => $th->getMessage()
            ]);
        }
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
