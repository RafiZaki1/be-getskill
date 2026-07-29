<?php

namespace App\Models;

use App\Base\Interfaces\HasCourse;
use App\Base\Interfaces\HasUser;
use App\Base\Interfaces\HasUserCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends Model implements HasUser, HasCourse
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'review', 'rating'];

    /**
     * Get the user that owns the CourseReview
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the course that owns the CourseReview
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
