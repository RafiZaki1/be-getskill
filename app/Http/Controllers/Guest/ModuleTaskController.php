<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModuleTaskController extends Controller
{
    /**
     * Daftar tugas pada modul
     */
    public function index($module): JsonResponse
    {
        // TODO: Implement index
        return response()->json(['message' => 'Module tasks list']);
    }

    /**
     * Pengumpulan tugas pada modul (file & link)
     */
    public function submitTask(Request $request, $task): JsonResponse
    {
        // TODO: Implement submitTask
        return response()->json(['message' => 'Submit module task']);
    }

    /**
     * Detail tugas apabila sudah mengumpulkan tugas pada modul (file & link)
     */
    public function showSubmission($submission): JsonResponse
    {
        // TODO: Implement showSubmission
        return response()->json(['message' => 'Task submission detail']);
    }
}
