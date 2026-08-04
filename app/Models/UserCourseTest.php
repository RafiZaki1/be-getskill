<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCourseTest extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    public $keyType = 'char';

    protected $fillable = [
        'user_id',
        'course_test_id',
        'module_question_id',
        'answer',
        'score',
        'test_type',
    ];

    /**
     * Get the user that owns the UserCourseTest
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courseTest that owns the UserCourseTest
     */
    public function courseTest(): BelongsTo
    {
        return $this->belongsTo(CourseTest::class);
    }
}
