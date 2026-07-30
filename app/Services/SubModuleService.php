<?php

namespace App\Services;

use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Interfaces\Course\SubModuleInterface;
use App\Enums\ContentImageEnum;
use App\Http\Resources\SubModuleResource;
use App\Models\ContentImage;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;

class SubModuleService
{
    private SubModuleInterface $subModule;
    private ModuleInterface $module;
    private QuizInterface $quiz;
    public function __construct(SubModuleInterface $subModule, ModuleInterface $module, QuizInterface $quiz)
    {
        $this->subModule = $subModule;
        $this->module = $module;
        $this->quiz = $quiz;
    }

    /**
     * next
     *
     * @param  mixed $subModule
     * @return mixed
     */
    public function next(mixed $subModule): mixed
    {
        $subModuleNext = $this->subModule->nextSubModule($subModule->step + 1, $subModule->module_id);
        if ($subModuleNext == null && $subModule->module->quizzes->first() != null) {
            return false;
        }
        $firstModuleNext = $this->module->moduleNextStep($subModule->module->step, $subModule->module->course->id);
        $subModuleInNextModule = $this->subModule->nextSubModule(1, $firstModuleNext->id);
        if ($subModuleNext) {
            return SubModuleResource::make($subModuleNext);
        } else if ($subModuleInNextModule) {
            return SubModuleResource::make($subModuleInNextModule);
        }
    }

    /**
     * prev
     *
     * @param  mixed $subModule
     * @return mixed
     */
    public function prev(mixed $subModule): mixed
    {
        $subModulePrev = $this->subModule->prevSubModule($subModule->step - 1, $subModule->module_id);
        if ($subModulePrev) {
            return SubModuleResource::make($subModulePrev);
        }
        $firstModulePrev = $this->module->modulePrevStep($subModule->module->step, $subModule->module->course->id);
        $subModuleInPrevModule = $this->subModule->prevSubModule($firstModulePrev->subModules->count(), $firstModulePrev->id);
        if ($subModuleInPrevModule) {
            return SubModuleResource::make($subModuleInPrevModule);
        }
    }

    public function getImages($content): mixed
    {
        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['blocks'])) {
            return [];
        }

        $imageBlocks = array_filter($data['blocks'], function ($block) {
            return isset($block['type']) && $block['type'] === 'image';
        });

        return $imageFilenames = array_map(function ($block) {
            return basename($block['data']['file']['url'] ?? '');
        }, $imageBlocks);
    }

    public function updateUsedImage($imageFilenames, $subModule): void
    {
        $imageQuery = ContentImage::query();

        if (count($imageFilenames) > 0) {
            foreach ($imageFilenames as $fileName) {
                $imageQuery->orWhere('path', "LIKE", "%$fileName%");
            }
            $images = $imageQuery->get();
        } else {
            $images = [];
        }
        ContentImage::where('sub_module_id', $subModule->id)->update(['used' => false]);
        foreach ($images as $image) {
            $image->update(['used' => true, 'sub_module_id' => $subModule->id]);
        }
    }
}
