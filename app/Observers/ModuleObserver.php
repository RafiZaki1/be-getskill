<?php

namespace App\Observers;

use App\Models\Module;
use Faker\Provider\Uuid;
use Illuminate\Support\Str;

class ModuleObserver
{
    /**
     * Method creating
     *
     * @param Module $module [explicite description]
     *
     * @return void
     */
    public function creating(Module $module): void
    {
        $course_slug = Str::slug($module->course->title);
        $module_slug = Str::slug($module->title);
        $module->slug = $course_slug .'-'.$module_slug;
    }
    /**
     * Method updating
     *
     * @param Module $module [explicite description]
     *
     * @return void
     */
    public function updating(Module $module): void
    {
        $course_slug = Str::slug($module->course->title);
        $module_slug = Str::slug($module->title);
        $module->slug = $course_slug .'-'. $module_slug;
    }
}
