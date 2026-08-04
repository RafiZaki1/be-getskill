<?php

namespace App\Http\Controllers\Course;

use App\Models\CourseTask;
use App\Models\ModuleTask;
use Illuminate\Http\Request;
use App\Models\SubmissionTask;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\SubmissionTaskService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CourseTaskRequest;
use App\Http\Resources\ModuleTaskResource;
use App\Http\Requests\SubmissionTaskRequest;
use App\Http\Requests\UpdateGradeTaskRequest;
use App\Http\Resources\SubmissionTaskResource;
use App\Http\Resources\ShowSubmissionTaskResource;
use App\Contracts\Interfaces\Course\SubmissionTaskInterface;
use App\Models\Module;
use OpenApi\Attributes as OA;

class SubmissionTaskController extends Controller
{
    private SubmissionTaskInterface $submissionTask;
    private SubmissionTaskService $service;
    /**
     * Method __construct
     *
     * @param SubmissionTaskInterface $submissionTask [explicite description]
     *
     * @return void
     */
    public function __construct(SubmissionTaskInterface $submissionTask, SubmissionTaskService $service)
    {
        $this->submissionTask = $submissionTask;
        $this->service = $service;
    }
    #[OA\Get(
        path: "/api/module-task/{module_task}/submissions",
        operationId: "adminSubmissionTaskIndex",
        summary: "Daftar pengumpulan tugas pada modul (Admin)",
        description: "Melihat daftar user yang telah mengumpulkan tugas",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Module Tasks"]
    )]
    #[OA\Parameter(name: "module_task", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil mengambil daftar pengumpulan tugas")]
    public function index(ModuleTask $moduleTask, Request $request): JsonResponse
    {
        try {
            $submissionTasks = $this->submissionTask->getWhereSearch(['module_task_id' => $moduleTask->id], $request);
            return ResponseHelper::success(SubmissionTaskResource::collection($submissionTasks), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * show
     *
     * @param  mixed $submissionTask
     * @return JsonResponse
     */
    public function show(SubmissionTask $submissionTask): JsonResponse
    {
        try {
            return ResponseHelper::success(ShowSubmissionTaskResource::make($submissionTask), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method store
     *
     * @param SubmissionTaskRequest $request [explicite description]
     * @param ModuleTask $moduleTask [explicite description]
     *
     * @return JsonResponse
     */
    public function store(SubmissionTaskRequest $request, ModuleTask $moduleTask)
    {
        $data = $request->validated();
        $data['module_task_id'] = $moduleTask->id;
        $data['user_id']        = auth()->user()->id;
        $data['file']           = $this->service->handleStoreFile($request);
        $data['answer']         = null;

        if($file = $moduleTask->submissionTask()->where(['user_id' => $data['user_id'], 'module_task_id' => $data['module_task_id']])->first()?->file) {
            $this->service->handleRemoveFile($file);
        }

        $stored = $this->submissionTask->store($data);
        if ($stored['status'] === 'created') {
            if (auth()->user()->hasRole('guest')) {
                $this->service->handleAddPoint($moduleTask->point);
            }
            return ResponseHelper::success(true, trans('alert.add_success'));
        } elseif ($stored['status'] === 'updated') {
            return ResponseHelper::success(true, trans('alert.update_success'));
        }
        return ResponseHelper::error(null, trans('alert.add_failed'));
    }

    /**
     * Store a link-based submission
     */
    public function storeLink(SubmissionTaskRequest $request, ModuleTask $moduleTask)
    {
        $data = $request->validated();
        $data['module_task_id'] = $moduleTask->id;
        $data['user_id']        = auth()->user()->id;
        $data['file']           = null;

        if($file = $moduleTask->submissionTask()->where(['user_id' => $data['user_id'], 'module_task_id' => $data['module_task_id']])->first()?->file) {
            $this->service->handleRemoveFile($file);
        }

        $stored = $this->submissionTask->store($data);
        if ($stored['status'] === 'created') {
            if (auth()->user()->hasRole('guest')) {
                $this->service->handleAddPoint($moduleTask->point);
            }
            return ResponseHelper::success(true, trans('alert.add_success'));
        } elseif ($stored['status'] === 'updated') {
            return ResponseHelper::success(true, trans('alert.update_success'));
        }
        return ResponseHelper::error(null, trans('alert.add_failed'));
    }

    /**
     * Method update
     *
     * @param SubmissionTaskRequest $request [explicite description]
     * @param SubmissionTask $submissionTask [explicite description]
     *
     * @return JsonResponse
     */
    public function update(SubmissionTaskRequest $request, SubmissionTask $submissionTask): JsonResponse
    {
        try {
            $this->submissionTask->update($submissionTask->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method destroy
     *
     * @param SubmissionTask $submissionTask [explicite description]
     *
     * @return JsonResponse
     */
    public function destroy(SubmissionTask $submissionTask): JsonResponse
    {
        try {
            $this->submissionTask->delete($submissionTask->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans(null, trans('alert.delete_failed') . '. ' . $th->getMessage()));
        }
    }

    public function download(SubmissionTask $submissionTask)
    {
        $filePath = storage_path('app/public/' . $submissionTask->file);
        $originalFileName = basename($submissionTask->file); 
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File tidak ditemukan.'], 404);
        }

        return response()->download($filePath, $originalFileName);
    }

    /**
     * Method updateGrade
     * Updates only the grade column of a submission task
     *
     * @param UpdateGradeTaskRequest $request
     * @param SubmissionTask $submissionTask
     *
     * @return JsonResponse
     */
    public function updateGrade(UpdateGradeTaskRequest $request, SubmissionTask $submissionTask): JsonResponse
    {
        try {
            $validated = $request->validated();

            $oldGrade = $submissionTask->grade;

            $this->submissionTask->update(
                $submissionTask->id,
                ['grade' => $validated['grade']]
            );

            if ($oldGrade === null) {
                $submissionTask = $submissionTask->fresh('moduleTask', 'user');

                $userToAward  = $submissionTask->user;
                $pointForTask = $submissionTask->moduleTask->point;

                if ($userToAward->hasRole('student') || $userToAward->hasRole('guest')) {
                    $userToAward->increment('point', $pointForTask);
                }
            }

            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }
}
