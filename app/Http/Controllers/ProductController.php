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
use App\Models\ActivityManager;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function listProductByToko()
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Product',
                'deskripsi' => 'Manager melihat list product yang ada di toko',
            ]);
        }
        $idToko = Kasir::where('fk_id_user', auth()->user()->id)->first();
        $products = Product::where('fk_id_toko', $idToko->fk_id_toko)->get();
        return response()->json([
            'id' => '1',
            'data' => $products
        ]);
    }

    public function listProductByIdToko($id)
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Product',
                'deskripsi' => 'Manager melihat detail toko',
            ]);
        }

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

        // Ambil semua tambah_stocks yang relevan
        $tambahStocks = TambahStock::whereIn('fk_id_product', $productIds)
            ->orderBy('expired', 'asc')
            ->get()
            ->groupBy('fk_id_product');

        $jumlahProdukExpired = 0;

        foreach ($tambahStocks as $productId => $stocks) {
            // Ambil expired terdekat dari hari ini (boleh lewat hari ini sedikit atau belum lewat)
            $expiredTerdekat = $stocks->first();

            if ($expiredTerdekat && Carbon::parse($expiredTerdekat->expired)->lt($today)) {
                $jumlahProdukExpired++;
            }
        }

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
            if (auth()->user()->level == '1') {
                ActivityManager::create([
                    'name' => auth()->user()->name,
                    'activity' => 'Product',
                    'deskripsi' => 'Manager menambahkan product baru',
                ]);
            }
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
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Product',
                'deskripsi' => 'Manager melihat detail product',
            ]);
        }
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
            'harga_grosir' => 'required',
            'sk_grosir' => 'required',
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
        $products->harga_grosir = $validateData['harga_grosir'];
        $products->sk_grosir = $validateData['sk_grosir'];
        $products->save();

        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Product',
                'deskripsi' => 'Manager mengupdate harga product' . $products->nama_product . ' menjadi ' . $validateData['harga_jual'],
            ]);
        }

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

            if (auth()->user()->level == '1') {
                ActivityManager::create([
                    'name' => auth()->user()->name,
                    'activity' => 'Product',
                    'deskripsi' => 'Manager menghapus product',
                ]);
            }
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

        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Product',
                'deskripsi' => 'Manager mencari product product',
            ]);
        }

        return response()->json([
            'success' => true,
            'count' => $results->count(),
            'data' => $results,
        ]);
    }
}
