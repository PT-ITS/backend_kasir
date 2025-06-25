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
            $tebusMurah = TebusMurah::where('fk_id_toko', $id)->get();

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
            $today = Carbon::now()->startOfDay();
            $tebusMurah = TebusMurah::where('fk_id_toko', $id)
                ->whereDate('end', '>=', $today)
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
            'fk_id_toko' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
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
                'fk_id_toko' => $request->fk_id_toko,
                'start' => $request->start,
                'end' => $request->end
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
            'fk_id_toko' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
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
                'fk_id_toko' => $request->fk_id_toko,
                'start' => $request->start,
                'end' => $request->end
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
