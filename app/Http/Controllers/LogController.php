<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity;

class LogController extends Controller
{
    public function listLogActivity()
    {
        $logActivities = LogActivity::all();
        return response()->json([
            'id' => '1',
            'data' => $logActivities
        ]);
    }
}
