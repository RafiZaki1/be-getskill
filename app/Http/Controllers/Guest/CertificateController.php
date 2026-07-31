<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CertificateController extends Controller
{
    #[OA\Post(
        path: "/api/certificates/verify-name",
        operationId: "verifyCertificateName",
        summary: "Verifikasi nama untuk klaim sertifikat kursus",
        description: "Memverifikasi nama user sebelum mencetak sertifikat",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name_on_certificate"],
            properties: [
                new OA\Property(property: "name_on_certificate", type: "string", example: "Rafi Zaki")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Nama terverifikasi",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Verify certificate name")
            ]
        )
    )]
    public function verifyName(Request $request): JsonResponse
    {
        // TODO: Implement verifyName
        return response()->json(['message' => 'Verify certificate name']);
    }

    #[OA\Get(
        path: "/api/certificates/download/{code}",
        operationId: "downloadCertificate",
        summary: "Download sertifikat kursus",
        description: "Mengunduh file sertifikat kursus",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "code",
        description: "Certificate Code",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "File Sertifikat",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Download certificate")
            ]
        )
    )]
    public function download($code): JsonResponse
    {
        // TODO: Implement download
        return response()->json(['message' => 'Download certificate']);
    }
}
