<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity;

class LogController extends Controller
{
    public function listLogActivity()
    {
        $logActivities = LogActivity::orderBy('created_at', 'desc') // urutkan berdasarkan waktu terbaru
            ->take(6) // ambil 6 data saja
            ->get();

        return response()->json([
            'id' => '1',
            'data' => $logActivities
        ]);
    }
}
