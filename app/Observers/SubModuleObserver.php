<?php

namespace App\Observers;

use App\Models\SubModule;
use Faker\Provider\Uuid;
use Illuminate\Support\Str;

class SubModuleObserver
{
    /**
     * Method creating
     *
     * @param SubModule $subModule [explicite description]
     *
     * @return void
     */
    public function creating(SubModule $subModule): void
    {
        $sub_module_slug = Str::slug($subModule->title);
        $module_slug = Str::slug($subModule->module->title);
        $course_slug = Str::slug($subModule->module->course->title);
        $subModule->slug = $course_slug .'-'. $module_slug.'-' .'-'. $sub_module_slug;
    }
    /**
     * Method updating
     *
     * @param SubModule $subModule [explicite description]
     *
     * @return void
     */
    public function updating(SubModule $subModule): void
    {
        $sub_module_slug = Str::slug($subModule->title);
        $module_slug = Str::slug($subModule->module->title);
        $course_slug = Str::slug($subModule->module->course->title);
        $subModule->slug = $course_slug .'-'. $module_slug.'-' .'-'. $sub_module_slug;
    }
}
