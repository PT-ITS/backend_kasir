<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TebusMurah;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;

class TebusMurahController extends Controller
{
    public function listTebusMurahByIdToko($id)
    {
        try {
            $tebusMurah = TebusMurah::whereHas('product', function ($q) use ($id) {
                $q->where('fk_id_toko', $id);
            })->with('product')->get();

            return response()->json([
                'id' => '1',
                'data' => $tebusMurah
            ]);
        } catch (Exception $e) {
            return response()->json([
                'id' => '0',
                'message' => 'Gagal mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listTebusMurahByIdTokoWhereActive($id)
    {
        try {
            $today = Carbon::today()->toDateString();
            $tebusMurah = TebusMurah::whereHas('product', function ($q) use ($id) {
                $q->where('fk_id_toko', $id);
            })
                ->where('end', '>', $today)
                ->with('product')
                ->get();

            return response()->json([
                'id' => '1',
                'data' => $tebusMurah
            ]);
        } catch (Exception $e) {
            return response()->json([
                'id' => '0',
                'message' => 'Gagal mengambil data aktif.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createTebusMurah(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'harga' => 'required|string',
            'minimal_belanja' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'fk_id_product' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'id' => '0',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tebusMurah = TebusMurah::create([
                'harga' => $request->harga,
                'minimal_belanja' => $request->minimal_belanja,
                'start' => $request->start,
                'end' => $request->end,
                'fk_id_product' => $request->fk_id_product
            ]);

            return response()->json([
                'id' => '1',
                'data' => $tebusMurah
            ]);
        } catch (Exception $e) {
            return response()->json([
                'id' => '0',
                'message' => 'Gagal membuat data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTebusMurah(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'harga' => 'required|string',
            'minimal_belanja' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'fk_id_product' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'id' => '0',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tebusMurah = TebusMurah::find($id);

            if (!$tebusMurah) {
                return response()->json([
                    'id' => '0',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $tebusMurah->update([
                'harga' => $request->harga,
                'minimal_belanja' => $request->minimal_belanja,
                'start' => $request->start,
                'end' => $request->end,
                'fk_id_product' => $request->fk_id_product
            ]);

            return response()->json([
                'id' => '1',
                'data' => $tebusMurah
            ]);
        } catch (Exception $e) {
            return response()->json([
                'id' => '0',
                'message' => 'Gagal memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTebusMurah($id)
    {
        try {
            $tebusMurah = TebusMurah::find($id);

            if (!$tebusMurah) {
                return response()->json([
                    'id' => '0',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $tebusMurah->delete();

            return response()->json([
                'id' => '1',
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'id' => '0',
                'message' => 'Gagal menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
