<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Repositories\Course\ModuleTaskRepository;
use App\Contracts\Repositories\Course\SubmissionTaskRepository;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleTaskRequest;

use App\Http\Resources\ModuleTaskAnswerResource;
use App\Http\Resources\ModuleTaskResource;

use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleTask;
use App\Services\Course\ModuleTaskService;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ModuleTaskController extends Controller
{
    use PaginationTrait;

    private ModuleTaskRepository $moduleTask;
    private SubmissionTaskRepository $submissionTask;


    /**
     * Method __construct
     *
     * @param ModuleTaskRepository $moduleTask [explicite description]
     *
     * @return void
     */
    public function __construct(ModuleTaskRepository $moduleTask, SubmissionTaskRepository $submissionTask, ModuleTaskService $moduleTaskService)
    {
        $this->moduleTask        = $moduleTask;
        $this->submissionTask    = $submissionTask;
        $this->moduleTaskService = $moduleTaskService;
    }
    /**
     * Method index
     *
     * @param Course $course [explicite description]
     *
     * @return JsonResponse
     */
    public function index(Module $module): JsonResponse
    {
        try {
            $moduleTasks = $this->moduleTask->getWhere(['module_id' => $module->id]);
            return ResponseHelper::success(ModuleTaskResource::collection($moduleTasks), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('index course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method store
     *
     * @param ModuleTaskRequest $request [explicite description]
     * @param Module $module [explicite description]
     *
     * @return JsonResponse
     */
    public function store(ModuleTaskRequest $request, Module $module): JsonResponse
    {
        $data = $request->validated();

        try {
            $data['module_id'] = $module->id;
            $this->moduleTask->store($data);

            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Throwable $th) {
            Log::error('store course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, 'Terjadi kesalahan saat menyimpan tugas. ' . $th->getMessage());
        }
    }
    /**
     * Method show
     *
     * @param ModuleTask $moduleTask [explicite description]
     *
     * @return JsonResponse
     */
    public function show(Request $request, ModuleTask $moduleTask): JsonResponse
    {
        try {
            $moduleTask = $this->moduleTask
                ->searchUser($request)
                ->findOrFail($moduleTask->id);

            return ResponseHelper::success(
                ModuleTaskResource::make($moduleTask),
                trans('alert.fetch_success')
            );
        } catch (\Throwable $th) {
            Log::error('show course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method show with the answer from the task
     *
     * @param ModuleTask $moduleTask [explicite description]
     *
     * @return JsonResponse
     */
    public function showWithAnswer(ModuleTask $moduleTask): JsonResponse
    {
        try {
            $moduleTask = $this->moduleTask->show($moduleTask->id);
            return ResponseHelper::success(ModuleTaskAnswerResource::make($moduleTask), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('showWithAnswer course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function downloadAll(Request $request, ModuleTask $moduleTask)
    {
        try {
            $classroomId = $request->query('classroom_id');
            $zipPath = $this->moduleTaskService->createZipOfAllSubmissions($moduleTask, $classroomId);

            // Buat URL publik untuk frontend
            $zipFileName = basename($zipPath);
            $downloadUrl = url('storage/temp/' . $zipFileName);

            return ResponseHelper::success(['download_url' => $downloadUrl,], trans('File ZIP berhasil dibuat.'));
        } catch (\Throwable $e) {
            Log::error('downloadAll course ModuleTask error: ' . $e->getMessage());
            return ResponseHelper::error(null, 'Terjadi kesalahan saat menyiapkan file ZIP. ' . $e->getMessage());
        }
    }

    /**
     * Method update
     *
     * @param ModuleTaskRequest $request [explicite description]
     * @param ModuleTask $moduleTask [explicite description]
     *
     * @return JsonResponse
     */
    public function update(ModuleTaskRequest $request, ModuleTask $moduleTask): JsonResponse
    {
        $data = $request->validated();

        try {
            $this->moduleTask->update($moduleTask->id, $data);

            return ResponseHelper::success($moduleTask->module->id, trans('alert.update_success'));
        } catch (\Throwable $th) {
            Log::error('update course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, 'Terjadi kesalahan saat memperbarui tugas. ' . $th->getMessage());
        }
    }
    /**
     * Method destroy
     *
     * @param ModuleTask $moduleTask [explicite description]
     *
     * @return JsonResponse
     */
    public function destroy(ModuleTask $moduleTask): JsonResponse
    {
        try {
            $this->moduleTask->delete($moduleTask->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::success(true, trans('alert.delete_constrained'));
        }
    }

    public function getByCourse(string $slug)
    {
        try {
            $moduleTask = $this->moduleTask->getByCourse($slug);
            return ResponseHelper::success(ModuleTaskResource::collection($moduleTask), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('getByCourse course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function getByCourseWithPaginate(string $slug, Request $request)
    {
        $data = [];
        try {
            if ($request->has('page')) {
                $moduleTasks      = $this->moduleTask->getByCourseWithPaginate($request, $slug);
                $data['paginate'] = $this->customPaginate($moduleTasks->currentPage(), $moduleTasks->lastPage());
                $data['data']     = ModuleTaskResource::collection($moduleTasks);
            } else {
                $moduleTasks  = $this->moduleTask->getByCourseWithPaginate($request, $slug);
                $data['data'] = ModuleTaskResource::collection($moduleTasks);
            }

            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('getByCourseWithPaginate course ModuleTask error: ' . $th->getMessage());
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }


}
