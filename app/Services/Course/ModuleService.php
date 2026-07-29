<?php

namespace App\Services\Course;

use App\Base\Interfaces\uploads\ShouldHandleFileUpload;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Repositories\Course\SubModuleRepository;
use App\Enums\UploadDiskEnum;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Module;
use App\Models\User;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\DB;

class ModuleService implements ShouldHandleFileUpload
{
    private ModuleInterface $module;
    private SubModuleRepository $subModule;
    public function __construct(ModuleInterface $module, SubModuleRepository $subModule)
    {
        $this->module = $module;
        $this->subModule = $subModule;
    }

    use UploadTrait;

    public function delete(Module $module): array|bool
    {
        $modules = $this->module->getWhere('step', '>', $module->step);

        $modules->each(function ($mod) {
            $mod->decrement('step');
        });

        $this->module->delete($module->id);

        return true;
    }


    public function arrangeSubModuleSteps(Module $module): void
    {
        $subModules = $module->subModules()->orderBy('step')->get();

        $case = 'CASE id ';
        foreach ($subModules as $index => $submodule) {
            $case .= "WHEN {$submodule->id} THEN " . ($index + 1) . ' ';
        }
        $case .= 'END';

        $this->subModule->bulkUpdateById($subModules->pluck('id'), ['step' => DB::raw($case)]);
    }

}
