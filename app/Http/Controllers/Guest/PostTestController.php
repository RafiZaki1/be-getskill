<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class PostTestController extends Controller
{
    #[OA\Get(
        path: "/api/post-test/{course}",
        operationId: "postTestInformation",
        summary: "Pembuka post-test (informasi terkait pengerjaan post-test)",
        description: "Menampilkan informasi sebelum memulai post-test",
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
        description: "Informasi post-test",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Post-test information")
            ]
        )
    )]
    public function showInformation($course): JsonResponse
    {
        // TODO: Implement showInformation
        return response()->json(['message' => 'Post-test information']);
    }

    #[OA\Post(
        path: "/api/post-test/{course}",
        operationId: "submitPostTest",
        summary: "Pengerjaan post-test",
        description: "Mengirimkan jawaban post-test",
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
        description: "Post-test berhasil disubmit",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Submit post-test")
            ]
        )
    )]
    public function store(Request $request, $course): JsonResponse
    {
        // TODO: Implement store
        return response()->json(['message' => 'Submit post-test']);
    }

    #[OA\Get(
        path: "/api/post-test/result/{course}",
        operationId: "postTestResult",
        summary: "Detail hasil pengerjaan post-test",
        description: "Melihat hasil pengerjaan post-test",
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
        description: "Hasil post-test",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Post-test result")
            ]
        )
    )]
    public function showResult($course): JsonResponse
    {
        // TODO: Implement showResult
        return response()->json(['message' => 'Post-test result']);
    }
}
