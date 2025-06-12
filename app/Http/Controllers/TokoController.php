<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;

class TokoController extends Controller
{
    public function list()
    {
        try {
            $datas = Toko::all();
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
            $datas = Toko::find($id);
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
                'nama_toko' => 'required',
                'hp_toko' => 'required',
                'alamat_toko' => 'required',
                'fk_id_manager' => 'required|exists:users,id'
            ]);

            $datas = Toko::create([
                'nama_toko' => $validateData['nama_toko'],
                'hp_toko' => $validateData['hp_toko'],
                'alamat_toko' => $validateData['alamat_toko'],
                'fk_id_manager' => $validateData['fk_id_manager'],
            ]);

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
                'nama_toko' => 'required',
                'hp_toko' => 'required',
                'alamat_toko' => 'required',
                'fk_id_manager' => 'required|exists:users,id'
            ]);
            $datas = Toko::find($id);
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }
            $datas->nama_toko = $validateData['nama_toko'];
            $datas->hp_toko = $validateData['hp_toko'];
            $datas->alamat_toko = $validateData['alamat_toko'];
            $datas->fk_id_manager = $validateData['fk_id_manager'];
            $datas->save();

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
            $datas = Toko::find($id);
            if ($datas) {
                $datas->delete();
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
