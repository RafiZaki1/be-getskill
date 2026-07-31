<?php

namespace App\Http\Controllers\Course;

use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleTask;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleRequest;
use Illuminate\Support\Facades\Cache;
use App\Services\Course\ModuleService;
use App\Http\Resources\Course\ModuleResource;
use App\Http\Resources\Course\ModuleListResource;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Http\Resources\Course\ModuleSidebarResource;
use App\Http\Resources\Course\ModuleNoDetailResource;
use App\Contracts\Interfaces\Course\SubModuleInterface;
use App\Http\Resources\Course\CourseModuleListResource;
use App\Contracts\Interfaces\Course\ModuleTaskInterface;
use App\Http\Resources\Course\CourseModuleSimpleResource;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class ModuleController extends Controller
{
    private ModuleInterface $module;
    private ModuleService $service;
    private CourseInterface $course;
    private SubModuleInterface $subModule;
    private ModuleTaskInterface $moduleTask;
    /**
     * Method __construct
     *
     * @param ModuleInterface $module [explicite description]
     *
     * @return void
     */
    public function __construct(ModuleInterface $module, SubModuleInterface $subModule, CourseInterface $course, ModuleService $service, ModuleTaskInterface $moduleTask)
    {
        $this->module = $module;
        $this->service = $service;
        $this->course = $course;
        $this->subModule = $subModule;
        $this->moduleTask = $moduleTask;
    }
    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function index(string $slug, Request $request): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $request->merge(['course_id' => $course->id]);
            $modules = $this->module->search($request);
            return ResponseHelper::success(ModuleResource::collection($modules), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method indexList
     *
     * @param string $slug
     * @param Request $request
     * @return JsonResponse
     */
    public function indexList(string $slug, Request $request): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $request->merge(['course_id' => $course->id]);
            $modules = $this->module->search($request);
            return ResponseHelper::success(ModuleListResource::collection($modules), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method showBySlug
     *
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function showByCourseSlug(string $slug): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $data = ['course_id' => $course->id];
            $modules = $this->module->search(new Request($data));
            return ResponseHelper::success(ModuleNoDetailResource::collection($modules), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    #[OA\Post(
        path: "/api/modules/{slug}",
        operationId: "adminModuleStore",
        summary: "Tambah modul kursus (Admin)",
        description: "Menambahkan modul baru pada sebuah kursus berdasarkan slug",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Modules"]
    )]
    #[OA\Parameter(name: "slug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "Pendahuluan"),
                new OA\Property(property: "description", type: "string", example: "Deskripsi modul")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil menambahkan modul")]
    public function store(string $slug, ModuleRequest $request): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $data['step'] = ($this->module->getOneByCourse($course->id)?->step ?? 0) + 1;
    
            $this->module->store($data);
    
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Get(
        path: "/api/modules/detail/{module}",
        operationId: "adminModuleShow",
        summary: "Detail modul kursus (Admin)",
        description: "Melihat detail modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Modules"]
    )]
    #[OA\Parameter(name: "module", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil mengambil detail modul")]
    public function show(Module $module): JsonResponse
    {
        try {
            $module = $this->module->show($module->id);
            return ResponseHelper::success(new ModuleResource($module));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans(null, trans('alert.fetch_failed') . '. ' . $th->getMessage()));
        }
    }

    /**
     * Method showWithSlug
     *
     * @param string $slug 
     *
     * @return JsonResponse
     */
    public function showWithSlug(string $slug): JsonResponse
    {
        try {
            $module = $this->module->showWithSlug($slug);
            return ResponseHelper::success(new ModuleSidebarResource($module));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/modules/{module}",
        operationId: "adminModuleUpdate",
        summary: "Edit modul kursus (Admin)",
        description: "Mengedit data modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Modules"]
    )]
    #[OA\Parameter(name: "module", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "title", type: "string", example: "Pendahuluan Revisi"),
                new OA\Property(property: "description", type: "string", example: "Deskripsi modul revisi")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil mengupdate modul")]
    public function update(ModuleRequest $request, Module $module): JsonResponse
    {
        try {
            $this->module->update($module->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Delete(
        path: "/api/modules/{module}",
        operationId: "adminModuleDestroy",
        summary: "Hapus modul kursus (Admin)",
        description: "Menghapus modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Modules"]
    )]
    #[OA\Parameter(name: "module", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil menghapus modul")]
    public function destroy(Module $module): JsonResponse
    {
        try {
            $this->service->delete($module);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(false, trans('alert.delete_constrained'));
        }
    }

    /**
     * forward
     *
     * @param  mixed $module
     * @return JsonResponse
     */
    public function forward(Module $module): JsonResponse
    {
        try {
            $targetStep = $module->step + 1;

            // Ambil module target (step > current)
            $forwardModule = $this->module->getForward($module->step, $module->course_id);

            // Geser data yang konflik ke atas
            $this->module->shiftStepsUpward($targetStep, $module->course_id, $module->id);

            // Normalisasi ulang step agar urut
            $this->module->normalizeSteps($module->course_id);

            // Jika ada module tujuan, turunkan step-nya
            if ($forwardModule) {
                $forwardModule->decrement('step');
            }

            // Naikkan step modul saat ini setelah module lain sudah diatur
            $module->increment('step');

            return ResponseHelper::success([$module, $forwardModule], trans('alert.update_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(false, trans('alert.update_failed'));
        }
    }

    /**
     * backward
     *
     * @param  mixed $module
     * @return JsonResponse
     */
    public function backward(Module $module): JsonResponse
    {
        try {
            $backwardModule = $this->module->getBackward($module->step, $module->course->id);
            $backwardModule->increment('step');
            $module->decrement('step');
            return ResponseHelper::success([$module, $backwardModule], trans('alert.update_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(trans('alert.update_failed'));
        }
    }

    /**
     * listModule
     *
     * @param  mixed $slug
     * @return void
     */
    public function listModuleWithSubModul(string $slug, Request $request)
    {
        try {
            $subModule = $this->subModule->showWithSlug($slug);
            $request->merge(['course_id' => $subModule->module->course_id]);
    
            $cacheKey = 'modules_with_submodule:' . $slug ;
            $modules = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                return $this->module->search($request);
            });
    
            return ResponseHelper::success(ModuleSidebarResource::collection($modules));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function listModuleWithCourse(string $slug, Request $request)
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($slug);
            $request->merge(['course_id' => $course->id]);

            $cacheKey = 'modules_with_course:' . $slug;
            $modules = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
                return $this->module->search($request);
            });
            return ResponseHelper::success(CourseModuleListResource::collection($modules));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::success(null, trans('alert.fetch_failed'));
        }
    }

    /**
     * listModule
     *
     * @return JsonResponse
     */
    public function listModule(string $slug, Request $request): JsonResponse
    {
        try {
            $module = $this->module->showWithSlug($slug);
            $request->merge(['course_id' => $module->course_id]);
            $modules = $this->module->search($request);
            return ResponseHelper::success(ModuleSidebarResource::collection($modules));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/module/{module}/arrange-submodules",
        operationId: "adminModuleArrangeSubModules",
        summary: "Mengubah urutan materi pada modul (Admin)",
        description: "Mengatur ulang urutan materi (sub modul) di dalam sebuah modul",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Modules"]
    )]
    #[OA\Parameter(name: "module", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil mengatur urutan materi sub modul")]
    public function arrangeSubModules(Module $module)
    {
        DB::beginTransaction();
        try {

            $this->service->arrangeSubModuleSteps($module);

            DB::commit();
            return ResponseHelper::success(null, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * getModulesByCourseId
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getModulesByCourseId(Request $request): JsonResponse
    {
        try {
            $request->merge(['course_id' => $request->course_id]);
            $modules = $this->module->search($request);

            return ResponseHelper::success(
                CourseModuleSimpleResource::collection($modules),
                trans('alert.fetch_success')
            );
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
}
