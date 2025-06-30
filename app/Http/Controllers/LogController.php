<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity;
use App\Models\ActivityManager;

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

    /**
     * Get list of activity managers
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function monitoringManager()
    {
        $activityManagers = ActivityManager::orderBy('created_at', 'desc')->get();

        return response()->json([
            'id' => '1',
            'data' => $activityManagers
        ]);
    }
}
