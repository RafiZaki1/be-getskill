<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    /**
     * Pembuka kuis (informasi terkait pengerjaan kuis & berisi riwayat pengerjaan kuis)
     */
    public function showInformation($quiz): JsonResponse
    {
        // TODO: Implement showInformation
        return response()->json(['message' => 'Quiz information']);
    }

    /**
     * Pengerjaan kuis
     */
    public function store(Request $request, $quiz): JsonResponse
    {
        // TODO: Implement store
        return response()->json(['message' => 'Submit quiz']);
    }

    /**
     * Detail hasil pengerjaan kuis
     */
    public function showResult($quiz): JsonResponse
    {
        // TODO: Implement showResult
        return response()->json(['message' => 'Quiz result']);
    }
}
