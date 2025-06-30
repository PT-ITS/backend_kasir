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
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'fk_id_jadwal' => 'required|integer',
            'fk_id_kasir' => 'required|integer',
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
            'fk_id_jadwal' => $request->fk_id_jadwal,
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
    public function index()
    {
        $data = Absensi::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
