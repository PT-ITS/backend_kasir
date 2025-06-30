<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;

class JadwalController extends Controller
{
    public function listJadwalByToko($id)
    {
        $data = Jadwal::where('id_toko', $id)->get();

        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }
    
    public function createJadwal(Request $request)
    {
        $data = Jadwal::create($request->all());
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }

    public function updateJadwal(Request $request, $id)
    {
        $data = Jadwal::find($id);
        $data->update($request->all());
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }

    public function deleteJadwal($id)
    {
        $data = Jadwal::find($id);
        $data->delete();
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }
}
