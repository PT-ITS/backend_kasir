<?php

namespace App\Http\Controllers;

use App\Models\CatatanStock;
use App\Models\TambahStock;
use App\Models\Product;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    public function belanjaStock(Request $request)
    {
        $validated = $request->validate([
            'total_harga' => 'required|string',
            'bukti_nota' => 'required',
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

            // Save to storage
            $file = $validated['bukti_nota'];
            $filePath = $file->store('bukti_nota', 'public');
            $fileContent = file_get_contents($file->getRealPath());
            Storage::disk('public')->put($filePath, $fileContent);
            $buktiNotaName = $filePath;

            // Simpan catatan stok utama
            $catatanStock = CatatanStock::create([
                'total_harga' => $validated['total_harga'],
                'bukti_nota' => $buktiNotaName,
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


            LogActivity::create([
                'level' => auth()->user()->name,
                'nama' => auth()->user()->name,
                'keterangan' => 'Menambahkan stock',
            ]);
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
                'tanggal' => optional($item->catatanStock)->tanggal_belanja ?? '-',
                'product' => optional($item->product)->nama_product ?? '-',
                'jumlah' => $item->jumlah,
                'harga_beli' => $item->harga_beli,
            ];
        });

        return response()->json([
            'id' => '1',
            'data' => $result,
        ]);
    }

    public function listStockByIdToko($id)
    {
        $catatanStocks = CatatanStock::whereHas('tambahStocks.product', function ($query) use ($id) {
            $query->where('fk_id_toko', $id);
        })
            ->with(['tambahStocks.product'])
            ->get();
        return response()->json([
            'id' => '1',
            'message' => 'Success',
            'data' => $catatanStocks,
        ]);
        return response()->json($catatanStocks);
    }

    public function delete($id)
    {
        $catatan = CatatanStock::findOrFail($id);
        if ($catatan->bukti_nota) {
            Storage::disk('public')->delete($catatan->bukti_nota);
        }
        $catatan->tambahStocks()->delete(); // delete details first
        $catatan->delete();

        return response()->json([
            'id' => '1',
            'message' => 'Data deleted',
            'data' => []
        ]);
    }
}
