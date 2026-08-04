<?php

namespace App\Models;

use App\Base\Interfaces\HasCourse;
use App\Base\Interfaces\HasUserCourseTests;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseTest extends Model implements HasCourse, HasUserCourseTests
{
    use HasFactory, HasUuids;
    public $keyType = 'char';
    public $incrementing = false;
    protected $table = 'course_tests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'course_id',
        'total_question',
        'duration',
        'is_submitted',
    ];

    /**
     * Get the course that owns the CourseTest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course(): BelongsTo
    {
    return $this->belongsTo(Course::class);
    }
    /**
     * Get all of the userCourseTests for the CourseTest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCourseTests(): HasMany
    {
        return $this->hasMany(UserCourseTest::class);
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
