<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AbsensiController extends Controller
{
    /**
     * Menyimpan data absensi beserta foto.
     */
    public function absensi(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'shift' => 'required|string',
            'tanggal_absensi' => 'required|date',
            'jenis_absensi' => 'required|string',
            'fk_id_kasir' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan file foto ke storage/app/public/absensi
        $path = $request->file('foto')->store('absensi', 'public');

        // Simpan ke database
        $absensi = Absensi::create([
            'foto' => $path,
            'shift' => $request->shift,
            'tanggal_absensi' => $request->tanggal_absensi,
            'jenis_absensi' => $request->jenis_absensi,
            'fk_id_kasir' => $request->fk_id_kasir,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Absensi berhasil disimpan',
            'data' => $absensi
        ]);
    }

    /**
     * Menampilkan semua data absensi
     */
    public function list()
    {
        $data = Absensi::with(['user', 'kasir'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ]);
    }
}
