<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class PreTestController extends Controller
{
    #[OA\Get(
        path: "/api/pre-test/{course}",
        operationId: "preTestInformation",
        summary: "Pembuka pre-test (informasi terkait pengerjaan pre-test)",
        description: "Menampilkan informasi sebelum memulai pre-test",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "course",
        description: "Course Slug/ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Informasi pre-test",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Pre-test information")
            ]
        )
    )]
    public function showInformation($course): JsonResponse
    {
        // TODO: Implement showInformation
        return response()->json(['message' => 'Pre-test information']);
    }

    #[OA\Post(
        path: "/api/pre-test/{course}",
        operationId: "submitPreTest",
        summary: "Pengerjaan pre-test",
        description: "Mengirimkan jawaban pre-test",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "course",
        description: "Course Slug/ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["answers"],
            properties: [
                new OA\Property(
                    property: "answers",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "question_id", type: "string", example: "uuid-question"),
                            new OA\Property(property: "answer_id", type: "string", example: "uuid-answer")
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Pre-test berhasil disubmit",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Submit pre-test")
            ]
        )
    )]
    public function store(Request $request, $course): JsonResponse
    {
        // TODO: Implement store
        return response()->json(['message' => 'Submit pre-test']);
    }

    #[OA\Get(
        path: "/api/pre-test/result/{course}",
        operationId: "preTestResult",
        summary: "Detail hasil pengerjaan pre-test",
        description: "Melihat hasil pengerjaan pre-test",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "course",
        description: "Course Slug/ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Hasil pre-test",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Pre-test result")
            ]
        )
    )]
    public function showResult($course): JsonResponse
    {
        // TODO: Implement showResult
        return response()->json(['message' => 'Pre-test result']);
    }
}
