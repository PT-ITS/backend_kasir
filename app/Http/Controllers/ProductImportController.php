<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ActivityManager;

class ProductImportController extends Controller
{
    public function import(Request $request)
    {
        if (auth()->user()->level == '1') {
            ActivityManager::create([
                'name' => auth()->user()->name,
                'activity' => 'Import Product',
                'deskripsi' => 'Manager melakukan import product',
            ]);
        }
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Import produk berhasil!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import gagal!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
