<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\ProductPromo;
use Illuminate\Support\Facades\DB;

class ControllerPromo extends Controller
{
    public function listPromoByToko($id)
    {
        $promos = Promo::with('productPromos')
                    ->where('fk_id_toko', $id)
                    ->get();

        return response()->json([
            'id' => '1',
            'data' => $promos
        ]);
    }

    public function createPromoByToko(Request $request, $id)
    {
        $request->validate([
            'nama_promo' => 'required|string',
            'keterangan' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'products' => 'required|array',
            'products.*.fk_id_product' => 'required|integer',
            'products.*.harga' => 'required|numeric',
            'products.*.stock' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            $promo = Promo::create([
                'nama_promo' => $request->nama_promo,
                'keterangan' => $request->keterangan,
                'start' => $request->start,
                'end' => $request->end,
                'fk_id_toko' => $id
            ]);

            foreach ($request->products as $product) {
                $product['fk_id_promo'] = $promo->id;
                ProductPromo::create($product);
            }

            DB::commit();

            return response()->json([
                'id' => '1',
                'message' => 'Promo berhasil dibuat.',
                'data' => $promo->load('productPromos')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'id' => '0',
                'message' => 'Gagal membuat promo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePromo(Request $request, $promo_id)
    {
        $request->validate([
            'nama_promo' => 'required|string',
            'keterangan' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'products' => 'required|array',
            'products.*.fk_id_product' => 'required|integer',
            'products.*.harga' => 'required|numeric',
            'products.*.stock' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            $promo = Promo::findOrFail($promo_id);

            $promo->update([
                'nama_promo' => $request->nama_promo,
                'keterangan' => $request->keterangan,
                'start' => $request->start,
                'end' => $request->end,
            ]);

            // Hapus produk lama dan tambahkan yang baru
            ProductPromo::where('fk_id_promo', $promo->id)->delete();

            foreach ($request->products as $product) {
                $product['fk_id_promo'] = $promo->id;
                ProductPromo::create($product);
            }

            DB::commit();

            return response()->json([
                'id' => '1',
                'message' => 'Promo berhasil diperbarui.',
                'data' => $promo->load('productPromos')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'id' => '0',
                'message' => 'Gagal mengupdate promo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deletePromo($promo_id)
    {
        DB::beginTransaction();
        try {
            $promo = Promo::findOrFail($promo_id);
            ProductPromo::where('fk_id_promo', $promo->id)->delete();
            $promo->delete();

            DB::commit();

            return response()->json([
                'id' => '1',
                'message' => 'Promo berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'id' => '0',
                'message' => 'Gagal menghapus promo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

