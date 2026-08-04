<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleQuestionRequest;
use App\Http\Resources\ModuleQuestionAdminResource;
use App\Http\Resources\ModuleQuestionResource;
use App\Models\Module;
use App\Models\ModuleQuestion;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ModuleQuestionController extends Controller
{
    private ModuleQuestionInterface $moduleQuestion;
    public function __construct(ModuleQuestionInterface $moduleQuestion)
    {
        $this->moduleQuestion = $moduleQuestion;
    }
    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function index(Module $module): JsonResponse
    {
        try {
            $moduleQuestions = $this->moduleQuestion->getByModule($module->id);
            return ResponseHelper::success(ModuleQuestionResource::collection($moduleQuestions), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans(null, trans('alert.fetch_failed') . '. ' . $th->getMessage()));
        }
    }

    /**
     * index
     *
     * @param  mixed $module
     * @return JsonResponse
     */
    public function showAdmin(Module $module): JsonResponse
    {
        try {
            $moduleQuestions = $this->moduleQuestion->getByModule($module->id);
            return ResponseHelper::success(ModuleQuestionAdminResource::collection($moduleQuestions), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    #[OA\Post(
        path: "/api/module-questions/{module}",
        operationId: "adminModuleQuestionStore",
        summary: "Tambah soal untuk quiz pada modul (Admin)",
        description: "Menambahkan soal baru untuk kuis dalam sebuah modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Module Questions"]
    )]
    #[OA\Parameter(name: "module", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "question", type: "string", example: "Berapa 1 + 1?"),
                new OA\Property(property: "answer", type: "string", example: "A"),
                new OA\Property(property: "a", type: "string", example: "1"),
                new OA\Property(property: "b", type: "string", example: "2"),
                new OA\Property(property: "c", type: "string", example: "3"),
                new OA\Property(property: "d", type: "string", example: "4"),
                new OA\Property(property: "e", type: "string", example: "5")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil menambahkan soal")]
    public function store(ModuleQuestionRequest $request, Module $module): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['module_id'] = $module->id;
            $this->moduleQuestion->store($data);
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method edit
     * 
     * @param ModuleQuestion $moduleQuestion [explicite description]
     *
     * @return JsonResponse
     */
    public function edit(ModuleQuestion $moduleQuestion): JsonResponse
    {
        try {
            return ResponseHelper::success(ModuleQuestionResource::make($moduleQuestion), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/module-questions/{module_question}",
        operationId: "adminModuleQuestionUpdate",
        summary: "Edit soal untuk quiz pada modul (Admin)",
        description: "Mengedit soal quiz pada modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Module Questions"]
    )]
    #[OA\Parameter(name: "module_question", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "question", type: "string", example: "Berapa 1 + 2?"),
                new OA\Property(property: "answer", type: "string", example: "C"),
                new OA\Property(property: "a", type: "string", example: "1"),
                new OA\Property(property: "b", type: "string", example: "2"),
                new OA\Property(property: "c", type: "string", example: "3"),
                new OA\Property(property: "d", type: "string", example: "4"),
                new OA\Property(property: "e", type: "string", example: "5")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil mengupdate soal")]
    public function update(ModuleQuestionRequest $request, ModuleQuestion $moduleQuestion): JsonResponse
    {
        try {
            $this->moduleQuestion->update($moduleQuestion->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Delete(
        path: "/api/module-questions/{module_question}",
        operationId: "adminModuleQuestionDestroy",
        summary: "Hapus soal untuk quiz pada modul (Admin)",
        description: "Menghapus soal quiz pada modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Module Questions"]
    )]
    #[OA\Parameter(name: "module_question", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil menghapus soal")]
    public function destroy(ModuleQuestion $moduleQuestion): JsonResponse
    {
        try {
            $this->moduleQuestion->delete($moduleQuestion->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.delete_failed') . '. ' . $th->getMessage());
        }
    }
}
