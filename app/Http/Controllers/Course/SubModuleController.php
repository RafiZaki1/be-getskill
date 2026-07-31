<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\Course\SubModuleInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Enums\UploadDiskEnum;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubModuleRequest;
use App\Http\Resources\SubModuleResource;
use App\Models\ContentImage;
use App\Models\Module;
use App\Models\SubModule;
use App\Services\Course\ContentImageService;
use App\Services\SubModuleService;
use App\Traits\UploadTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubModuleController extends Controller
{
    use UploadTrait;
    private SubModuleInterface $subModule;
    private SubModuleService $service;
    private ContentImageService $contentImageService;
    private ModuleInterface $module;
    private UserCourseInterface $userCourse;
    public function __construct(SubModuleInterface $subModule, SubModuleService $service, UserCourseInterface $userCourse, ModuleInterface $module, ContentImageService $contentImageService)
    {
        $this->subModule = $subModule;
        $this->service = $service;
        $this->contentImageService = $contentImageService;
        $this->userCourse = $userCourse;
        $this->module = $module;
    }

    /**
     * Method store
     *
     * @param SubModuleRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function store(SubModuleRequest $request, Module $module): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['module_id'] = $module->id;
            $subModule = $this->subModule->getOneByModul($module->id);
            if ($subModule != null) {
                $data['step'] = $subModule->step + 1;
            } else {
                $data['step'] = 1;
            }


            $subModule = $this->subModule->store($data);
            $imageFilenames = $this->service->getImages($request->content);
            $this->service->updateUsedImage($imageFilenames, $subModule);

            $unusedImage = ContentImage::where('sub_module_id', $subModule->id)->where('used', false)->get();
            $this->contentImageService->delete($unusedImage);

            return ResponseHelper::success(SubModuleResource::make($subModule), trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method update
     *
     * @param SubModuleRequest $request [explicite description]
     * @param SubModule $subModule [explicite description]
     *
     * @return JsonResponse
     */
    public function update(SubModuleRequest $request, SubModule $subModule): JsonResponse
    {
        try {
            $imageFilenames = $this->service->getImages($request->content);
            $this->service->updateUsedImage($imageFilenames, $subModule);

            $unusedImage = ContentImage::where('sub_module_id', $subModule->id)->where('used', false)->get();
            $this->contentImageService->delete($unusedImage);

            $this->subModule->update($subModule->id, $request->validated());
            return ResponseHelper::success(SubModuleResource::make($subModule), trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * next
     *
     * @param  mixed $slug
     * @return void
     */
    public function next(string $slug)
    {
        $subModule = $this->subModule->showWithSlug($slug);
        $service = null;
        if ($subModule) {
            $service = $this->service->next($subModule);
        } else {
            $module = $this->module->showWithSlug($slug);
            $firstModuleNext = $this->module->moduleNextStep($module->step);
            $subModuleInNextModule = $this->subModule->nextSubModule(1, $firstModuleNext->id);
        }
        if ($service) {
            return ResponseHelper::success($service, trans('alert.fetch_success'));
        } else if ($service == false && $subModule) {
            return ResponseHelper::error($subModule->module->slug, 'Anda sudah pada halaman terakhir');
        } else {
            return ResponseHelper::success(SubModuleResource::make($subModuleInNextModule));
        }
    }

    /**
     * prev
     *
     * @param  mixed $slug
     * @return JsonResponse
     */
    public function prev(string $slug): JsonResponse
    {
        $subModule = $this->subModule->showWithSlug($slug);
        $service = null;
        if ($subModule) {
            $service = $this->service->prev($subModule);
        } else {
            $module = $this->module->showWithSlug($slug);
            $subModuleInPrevModule = $this->subModule->getOneByModul($module->id);
        }

        if ($service) {
            return ResponseHelper::success($service, trans('alert.fetch_success'));
        } else if ($service == false && $subModule) {
            $module = $this->module->modulePrevStep($subModule->module->step);
            return ResponseHelper::error($module->slug, 'Anda sudah pada halaman pertama');
        } else {
            return ResponseHelper::success(SubModuleResource::make($subModuleInPrevModule));
        }
    }

    /**
     * Method show
     *
     * @param string $slug [explicite description]
     *
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $subModule = $this->subModule->showWithSlug($slug);
            $subModule->prev = $this->subModule->prevSubModule($subModule->step - 1, $subModule->module_id);
            $subModule->next = $this->subModule->nextSubModule($subModule->step + 1, $subModule->module_id);
            return ResponseHelper::success(SubModuleResource::make($subModule), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * showAdmin
     *
     * @param  mixed $slug
     * @return JsonResponse
     */
    public function showAdmin(string $slug): JsonResponse
    {
        try {
            $subModule = $this->subModule->showWithSlug($slug);
            $subModule->prev = $this->subModule->prevSubModule($subModule->step - 1, $subModule->module_id);
            $subModule->next = $this->subModule->nextSubModule($subModule->step + 1, $subModule->module_id);
            return ResponseHelper::success(SubModuleResource::make($subModule), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    public function edit(SubModule $subModule): JsonResponse
    {
        try {
            return ResponseHelper::success(SubModuleResource::make($subModule));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method destroy
     *
     * @param SubModule $subModule [explicite description]
     *
     * @return JsonResponse
     */
    public function destroy(SubModule $subModule): JsonResponse
    {
        try {
            $this->subModule->delete($subModule->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(false, trans('alert.delete_constrained'));
        }
    }

    /**
     * uploadImage
     *
     * @param  mixed $request
     * @return void
     */
    public function uploadImage(Request $request)
    {
        Log::info("upload image");
        Log::info($request->all());
        Log::info($request->file('image'));

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if ($request->file('image')) {
                $url = $this->upload(UploadDiskEnum::IMAGE->value, $request->file('image'));

                if (!$url) {
                    throw new \Exception('Upload gagal. Storage::put() mengembalikan false.');
                }

                ContentImage::create(['path' => $url, 'user_id' => auth()->id()]);

                return response()->json([
                    'success' => 1,
                    'file' => ['url' => url('storage/' . $url)],
                    'user_id' => auth()->id(),
                ]);
            }

            return response()->json(['success' => 0, 'message' => 'Tidak ada file yang dikirim.']);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            Log::error('Upload Image Error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
            ]);

            return response()->json(['success' => 0, 'message' => 'File upload gagal.' . $th->getMessage()]);
        }
    }

    /**
     * checkPrevSubModule
     *
     * @return JsonResponse
     */
    public function checkPrevSubModule(string $slug): JsonResponse
    {
        try {
            $subModule = $this->subModule->showWithSlug($slug);
            $userCourse = $this->userCourse->showByCourse($subModule->module->course->id);
            $this->subModule->getAllPrevSubModule($userCourse->subModule->id, $userCourse->subModule->module->id);
            return ResponseHelper::success();
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }



    public function forward(SubModule $subModule): JsonResponse
    {
        try {
            $forwardSubModule = $this->subModule->getForward($subModule->step, $subModule->module_id);

            if (! $forwardSubModule) {
                return ResponseHelper::error(false, 'Data sudah berada di posisi paling akhir.', 400);
            }

            $this->subModule->swapStep($subModule, $forwardSubModule);

            return ResponseHelper::success(
                [$subModule->fresh(), $forwardSubModule->fresh()],
                trans('alert.update_success')
            );
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return ResponseHelper::error(false, trans('alert.update_failed'), 500);
        }
    }

    public function backward(SubModule $subModule): JsonResponse
    {
        try {
            $backwardSubModule = $this->subModule->getBackward($subModule->step, $subModule->module_id);

            if (! $backwardSubModule) {
                return ResponseHelper::error(false, 'Data sudah berada di posisi paling awal.', 400);
            }

            $this->subModule->swapStep($subModule, $backwardSubModule);

            return ResponseHelper::success(
                [$subModule->fresh(), $backwardSubModule->fresh()],
                trans('alert.update_success')
            );
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return ResponseHelper::error(false, trans('alert.update_failed'), 500);
        }
    }
}
