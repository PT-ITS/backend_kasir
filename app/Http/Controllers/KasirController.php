<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kasir;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function list()
    {
        try {
            $datas = User::join('kasirs', 'users.id', '=', 'kasirs.fk_id_user')
                ->join('tokos', 'kasirs.fk_id_toko', '=', 'tokos.id')
                ->select('users.*', 'kasirs.nama_kasir', 'kasirs.hp_kasir', 'kasirs.alamat_kasir', 'kasirs.fk_id_toko', 'tokos.nama_toko')
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

    public function listByToko($id)
    {
        try {
            $datas = User::join('kasirs', 'users.id', '=', 'kasirs.fk_id_user')
                ->join('tokos', 'kasirs.fk_id_toko', '=', 'tokos.id')
                ->where('kasirs.fk_id_toko', $id)
                ->select('users.*', 'kasirs.nama_kasir', 'kasirs.hp_kasir', 'kasirs.alamat_kasir', 'kasirs.fk_id_toko', 'tokos.nama_toko')
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
            $datas = User::join('kasirs', 'users.id', '=', 'kasirs.fk_id_user')
                ->select('users.*', 'kasirs.nama_kasir', 'kasirs.hp_kasir', 'kasirs.alamat_kasir')
                ->find($id);
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
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'nama_kasir' => 'required',
                'hp_kasir' => 'required',
                'alamat_kasir' => 'required',
                'fk_id_toko' => 'required|exists:tokos,id'
            ]);

            DB::beginTransaction();
            try {
                $datas = User::create([
                    'name' => $validateData['name'],
                    'email' => $validateData['email'],
                    'password' => bcrypt($validateData['password']),
                    'level' => '2',
                    'status' => '1',
                ]);

                Kasir::create([
                    'nama_kasir' => $validateData['nama_kasir'],
                    'hp_kasir' => $validateData['hp_kasir'],
                    'alamat_kasir' => $validateData['alamat_kasir'],
                    'fk_id_toko' => $validateData['fk_id_toko'],
                    'fk_id_user' => $datas->id,
                ]);

                DB::commit();
                return response()->json([
                    'id' => '1',
                    'message' => 'success',
                    'data' => $datas
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'id' => '0',
                    'message' => $th->getMessage(),
                    'data' => []
                ]);
            }
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
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'required',
                'nama_kasir' => 'required',
                'hp_kasir' => 'required',
                'alamat_kasir' => 'required',
                'fk_id_toko' => 'required|exists:tokos,id'
            ]);
            $datas = User::find($id);
            if (!$datas) {
                return response()->json([
                    'id' => '0',
                    'message' => 'data not found',
                    'data' => []
                ]);
            }

            DB::beginTransaction();
            try {
                $datas->name = $validateData['name'];
                $datas->email = $validateData['email'];
                $datas->password = bcrypt($validateData['password']);
                $datas->save();

                $idKasir = Kasir::where('fk_id_user', $id)->first();
                $dataKasir = Kasir::find($idKasir->id);
                $dataKasir->nama_kasir = $validateData['nama_kasir'];
                $dataKasir->hp_kasir = $validateData['hp_kasir'];
                $dataKasir->alamat_kasir = $validateData['alamat_kasir'];
                $dataKasir->fk_id_toko = $validateData['fk_id_toko'];
                $dataKasir->save();

                DB::commit();
                return response()->json([
                    'id' => '1',
                    'message' => 'success',
                    'data' => $datas
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'id' => '0',
                    'message' => $th->getMessage(),
                    'data' => []
                ]);
            }
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
            $idKasir = Kasir::where('fk_id_user', $id)->first();
            $datas = Kasir::find($idKasir->id);
            if ($datas) {
                // Delete user
                User::where('id', $id)->delete();
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
