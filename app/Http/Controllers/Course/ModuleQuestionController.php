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
    /**
     * Method store
     *
     * @param ModuleQuestionRequest $request [explicite description]
     * @param Module $module [explicite description]
     *
     * @return JsonResponse
     */
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

    /**
     * Method update
     *
     * @param ModuleQuestionRequest $request [explicite description]
     * @param ModuleQuestion $moduleQuestion [explicite description]
     *
     * @return JsonResponse
     */
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
    /**
     * Method destroy
     *
     * @param ModuleQuestion $moduleQuestion [explicite description]
     *
     * @return JsonResponse
     */
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
