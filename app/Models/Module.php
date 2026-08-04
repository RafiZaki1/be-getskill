<?php

namespace App\Models;

use App\Base\Interfaces\HasCourse;
use App\Base\Interfaces\HasModuleQuestions;
use App\Base\Interfaces\HasModuleTasks;
use App\Base\Interfaces\HasQuizzes;
use App\Base\Interfaces\HasSubModules;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model implements HasCourse, HasSubModules, HasModuleQuestions, HasQuizzes, HasModuleTasks
{
    use HasFactory, HasUuids;
    public $keyType = 'char';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $table = 'modules';
    protected $fillable = [
        'course_id',
        'step',
        'title',
        'slug',
        'sub_title'
    ];

    /**
     * Get the course that owns the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    /**
     * Get all of the subModules for the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subModules(): HasMany
    {
        return $this->hasMany(SubModule::class)->orderBy('step', 'asc');
    }
    /**
     * Method moduleQuestions
     *
     * @return HasMany
     */
    public function moduleQuestions(): HasMany
    {
        return $this->hasMany(ModuleQuestion::class);
    }
    /**
     * Get all of the quizzes for the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }
    /**
     * Get all of the moduleTasks for the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moduleTasks(): HasMany
    {
        return $this->hasMany(ModuleTask::class);
    }
    /**
     * Get all of the courseTestQuestions for the CourseTest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseTestQuestions(): HasMany
    {
        return $this->hasMany(CourseTestQuestion::class);
    }
}
