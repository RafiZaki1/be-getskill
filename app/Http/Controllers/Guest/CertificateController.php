<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    /**
     * Verifikasi nama untuk klaim sertifikat kursus
     */
    public function verifyName(Request $request): JsonResponse
    {
        // TODO: Implement verifyName
        return response()->json(['message' => 'Verify certificate name']);
    }

    /**
     * Download sertifikat kursus
     */
    public function download($code): JsonResponse
    {
        // TODO: Implement download
        return response()->json(['message' => 'Download certificate']);
    }
}
