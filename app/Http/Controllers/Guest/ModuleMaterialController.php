<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ModuleMaterialController extends Controller
{
    #[OA\Get(
        path: "/api/modules/{module}/material",
        operationId: "showModuleMaterial",
        summary: "Mempelajari materi pada setiap modul",
        description: "Menampilkan materi pelajaran untuk modul tertentu",
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
        description: "Materi Modul",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Module material")
            ]
        )
    )]
    public function showMaterial($module): JsonResponse
    {
        // TODO: Implement showMaterial
        return response()->json(['message' => 'Module material']);
    }
}
