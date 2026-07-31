<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostTestController extends Controller
{
    /**
     * Pembuka post-test (informasi terkait pengerjaan post-test)
     */
    public function showInformation($course): JsonResponse
    {
        // TODO: Implement showInformation
        return response()->json(['message' => 'Post-test information']);
    }

    /**
     * Pengerjaan post-test
     */
    public function store(Request $request, $course): JsonResponse
    {
        // TODO: Implement store
        return response()->json(['message' => 'Submit post-test']);
    }

    /**
     * Detail hasil pengerjaan post-test
     */
    public function showResult($course): JsonResponse
    {
        // TODO: Implement showResult
        return response()->json(['message' => 'Post-test result']);
    }
}
