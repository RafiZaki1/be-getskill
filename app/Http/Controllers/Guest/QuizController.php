<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class QuizController extends Controller
{
    #[OA\Get(
        path: "/api/quizzes/{quiz}",
        operationId: "quizInformation",
        summary: "Pembuka kuis (informasi terkait pengerjaan kuis & berisi riwayat pengerjaan kuis karna bisa saja remidi)",
        description: "Menampilkan informasi sebelum memulai kuis beserta riwayatnya",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "quiz",
        description: "Quiz ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Informasi kuis",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Quiz information")
            ]
        )
    )]
    public function showInformation($quiz): JsonResponse
    {
        // TODO: Implement showInformation
        return response()->json(['message' => 'Quiz information']);
    }

    #[OA\Post(
        path: "/api/quizzes/{quiz}",
        operationId: "submitQuiz",
        summary: "Pengerjaan kuis",
        description: "Mengirimkan jawaban kuis",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "quiz",
        description: "Quiz ID",
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
        description: "Kuis berhasil disubmit",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Submit quiz")
            ]
        )
    )]
    public function store(Request $request, $quiz): JsonResponse
    {
        // TODO: Implement store
        return response()->json(['message' => 'Submit quiz']);
    }

    #[OA\Get(
        path: "/api/quizzes/result/{quiz}",
        operationId: "quizResult",
        summary: "Detail hasil pengerjaan kuis",
        description: "Melihat hasil pengerjaan kuis",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "quiz",
        description: "Quiz ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Hasil kuis",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Quiz result")
            ]
        )
    )]
    public function showResult($quiz): JsonResponse
    {
        // TODO: Implement showResult
        return response()->json(['message' => 'Quiz result']);
    }
}
