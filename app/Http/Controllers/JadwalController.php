<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\ActivityManager;

class JadwalController extends Controller
{
    public function listJadwalByToko($id)
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Jadwal',
                'deskripsi' => 'Manager melihat list jadwal',
            ]);
        }
        $data = Jadwal::where('id_toko', $id)->get();

        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }

    public function createJadwal(Request $request)
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Jadwal',
                'deskripsi' => 'Manager menambahkan jadwal baru',
            ]);
        }
        $data = Jadwal::create($request->all());
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }

    public function updateJadwal(Request $request, $id)
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Jadwal',
                'deskripsi' => 'Manager mengupdate jadwal',
            ]);
        }
        $data = Jadwal::find($id);
        $data->update($request->all());
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }

    public function deleteJadwal($id)
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Jadwal',
                'deskripsi' => 'Manager menghapus jadwal',
            ]);
        }
        $data = Jadwal::find($id);
        $data->delete();
        return response()->json([
            'id' => '1',
            'data' => $data
        ]);
    }
}
