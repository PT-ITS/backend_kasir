<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiayaOperasional;
use App\Models\ActivityManager;

class BiayaOperasionalController extends Controller
{
    public function list()
    {
        try {

            if (auth()->user()->level == '1') {
                ActivityManager::create([
                    'name' => auth()->user()->name,
                    'activity' => 'Biaya Operasional',
                    'deskripsi' => 'Manager melihat list biaya operasional',
                ]);
            }
            $datas = BiayaOperasional::join('tokos', 'biaya_operasionals.fk_id_toko', '=', 'tokos.id')
                ->select('biaya_operasionals.*', 'tokos.nama_toko')
                ->get();
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $datas
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function detail($id)
    {
        try {
            if (auth()->user()->level == '1') {
                ActivityManager::create([
                    'name' => auth()->user()->name,
                    'activity' => 'Biaya Operasional',
                    'deskripsi' => 'Manager melihat detail biaya operasional',
                ]);
            }
            $datas = BiayaOperasional::join('tokos', 'biaya_operasionals.fk_id_toko', '=', 'tokos.id')
                ->select('biaya_operasionals.*', 'tokos.nama_toko')
                ->where('id', $id)
                ->first();
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            return response()->json([
                'id' => '1',
                'message' => 'data found',
                'data' => $datas
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validateData = $request->validate([
                'nama_operasional' => 'required',
                'waktu_operasional' => 'required',
                'tanggal_bayar' => 'required',
                'jumlah_biaya' => 'required',
                'fk_id_toko' => 'required|exists:tokos,id'
            ]);

            $datas = BiayaOperasional::create([
                'nama_operasional' => $validateData['nama_operasional'],
                'waktu_operasional' => $validateData['waktu_operasional'],
                'tanggal_bayar' => $validateData['tanggal_bayar'],
                'jumlah_biaya' => $validateData['jumlah_biaya'],
                'fk_id_toko' => $validateData['fk_id_toko'],
            ]);

            if (auth()->user()->level == '1') {
                ActivityManager::create([
                    'name' => auth()->user()->name,
                    'activity' => 'Biaya Operasional',
                    'deskripsi' => 'Manager menambah biaya operasional' . $validateData['nama_operasional'],
                ]);
            }

            return response()->json([
                'id' => '1',
                'message' => 'success',
                'data' => $datas
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'id' => '0',
                'message' => $e->errors(),
                'data' => []
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validateData = $request->validate([
                'nama_operasional' => 'required',
                'waktu_operasional' => 'required',
                'tanggal_bayar' => 'required',
                'jumlah_biaya' => 'required',
                'fk_id_toko' => 'required|exists:tokos,id'
            ]);
            $datas = BiayaOperasional::find($id);
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            $datas->nama_operasional = $validateData['nama_operasional'];
            $datas->waktu_operasional = $validateData['waktu_operasional'];
            $datas->tanggal_bayar = $validateData['tanggal_bayar'];
            $datas->jumlah_biaya = $validateData['jumlah_biaya'];
            $datas->fk_id_toko = $validateData['fk_id_toko'];
            $datas->save();

            if (auth()->user()->level == '1') {
                ActivityManager::create([
                    'name' => auth()->user()->name,
                    'activity' => 'Biaya Operasional',
                    'deskripsi' => 'Manager mengupdate biaya operasional' . $validateData['nama_operasional'],
                ]);
            }

            return response()->json([
                'id' => '1',
                'message' => 'success',
                'data' => $datas
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'id' => '0',
                'message' => $e->errors(),
                'data' => []
            ]);
        }
    }
    public function delete($id)
    {
        try {
            $datas = BiayaOperasional::find($id);
            if ($datas) {
                $datas->delete();

                if (auth()->user()->level == '1') {
                    ActivityManager::create([
                        'name' => auth()->user()->name,
                        'activity' => 'Biaya Operasional',
                        'deskripsi' => 'Manager menghapus biaya operasional',
                    ]);
                }
                return response()->json([
                    'id' => '1',
                    'message' => 'success',
                    'data' => $datas
                ]);
            } else {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'id' => '0',
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
