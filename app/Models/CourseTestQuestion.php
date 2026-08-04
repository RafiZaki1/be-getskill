<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestQuestion extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_test_id',
        'module_id',
        'question_count'
    ];
    /**
     * Get the courseTest that owns the CourseTestQuestion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseTest(): BelongsTo
    {
        return $this->belongsTo(CourseTest::class);
    }
    /**
     * Get the module that owns the CourseTestQuestion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
