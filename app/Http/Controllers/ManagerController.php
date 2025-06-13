<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Manager;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function list()
    {
        try {
            $datas = User::join('managers', 'users.id', '=', 'managers.fk_id_user')
                ->select('users.*', 'managers.nama_manager', 'managers.hp_manager', 'managers.alamat_manager')
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
            $datas = User::join('manager', 'users.id', '=', 'manager.fk_id_user')
                ->select('users.*', 'manager.nama_manager', 'manager.hp_manager', 'manager.alamat_manager')
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
                'nama_manager' => 'required',
                'hp_manager' => 'required',
                'alamat_manager' => 'required',
            ]);

            DB::beginTransaction();
            try {
                $datas = User::create([
                    'name' => $validateData['name'],
                    'email' => $validateData['email'],
                    'password' => bcrypt($validateData['password']),
                ]);

                Manager::create([
                    'nama_manager' => $validateData['nama_manager'],
                    'hp_manager' => $validateData['hp_manager'],
                    'alamat_manager' => $validateData['alamat_manager'],
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
                'nama_manager' => 'required',
                'hp_manager' => 'required',
                'alamat_manager' => 'required',
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

                $idManager = Manager::where('fk_id_user', $id)->first();
                $dataManager = Manager::find($idManager->id);
                $dataManager->nama_manager = $validateData['nama_manager'];
                $dataManager->hp_manager = $validateData['hp_manager'];
                $dataManager->alamat_manager = $validateData['alamat_manager'];
                $dataManager->save();

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
            $idManager = Manager::where('fk_id_user', $id)->first();
            $datas = Manager::find($idManager->id);
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
