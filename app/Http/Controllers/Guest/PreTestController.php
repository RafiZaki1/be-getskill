<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PreTestController extends Controller
{
    /**
     * Pembuka pre-test (informasi terkait pengerjaan pre-test)
     */
    public function showInformation($course): JsonResponse
    {
        // TODO: Implement showInformation
        return response()->json(['message' => 'Pre-test information']);
    }

    /**
     * Pengerjaan pre-test
     */
    public function store(Request $request, $course): JsonResponse
    {
        // TODO: Implement store
        return response()->json(['message' => 'Submit pre-test']);
    }

    /**
     * Detail hasil pengerjaan pre-test
     */
    public function showResult($course): JsonResponse
    {
        // TODO: Implement showResult
        return response()->json(['message' => 'Pre-test result']);
    }
}
