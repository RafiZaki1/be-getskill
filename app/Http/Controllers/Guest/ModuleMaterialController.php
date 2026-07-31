<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModuleMaterialController extends Controller
{
    /**
     * Mempelajari materi pada setiap modul
     */
    public function showMaterial($module): JsonResponse
    {
        // TODO: Implement showMaterial
        return response()->json(['message' => 'Module material']);
    }
}
