<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ModuleTaskController extends Controller
{
    #[OA\Get(
        path: "/api/modules/{module}/tasks",
        operationId: "listModuleTasks",
        summary: "Daftar tugas pada modul",
        description: "Menampilkan daftar tugas untuk modul tertentu",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "module",
        description: "Module ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Daftar tugas",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "List tasks")
            ]
        )
    )]
    public function index($module): JsonResponse
    {
        // TODO: Implement index
        return response()->json(['message' => 'List tasks']);
    }

    #[OA\Post(
        path: "/api/tasks/{task}/submit",
        operationId: "submitModuleTask",
        summary: "Pengumpulan tugas pada modul (file & link)",
        description: "Mengumpulkan hasil tugas modul berupa file atau link",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "task",
        description: "Task ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "file", type: "string", format: "binary", description: "File tugas"),
                    new OA\Property(property: "link", type: "string", description: "Link tugas jika ada")
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tugas berhasil dikumpulkan",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Submit task")
            ]
        )
    )]
    public function submitTask(Request $request, $task): JsonResponse
    {
        // TODO: Implement submitTask
        return response()->json(['message' => 'Submit task']);
    }

    #[OA\Get(
        path: "/api/tasks/submission/{submission}",
        operationId: "showTaskSubmission",
        summary: "Detail tugas apabila sudah mengumpulkan tugas pada modul (file & link)",
        description: "Menampilkan detail tugas yang sudah dikumpulkan",
        security: [["bearerAuth" => []]],
        tags: ["Guest Learning & Checkout"]
    )]
    #[OA\Parameter(
        name: "submission",
        description: "Submission ID",
        required: true,
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Detail pengumpulan tugas",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Task submission detail")
            ]
        )
    )]
    public function showSubmission($submission): JsonResponse
    {
        // TODO: Implement showSubmission
        return response()->json(['message' => 'Task submission detail']);
    }
}
