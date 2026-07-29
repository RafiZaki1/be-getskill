<?php

namespace App\Models;

use App\Base\Interfaces\HasAverageRating;
use App\Base\Interfaces\HasCourseReview;
use App\Base\Interfaces\HasCourseReviews;
use App\Base\Interfaces\HasModules;
use App\Base\Interfaces\HasOneCourseTest;
use App\Base\Interfaces\HasSubCategory;
use App\Base\Interfaces\HasUser;
use App\Base\Interfaces\HasUserCourses;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model implements HasSubCategory, HasModules, HasUserCourses, HasCourseReviews, HasUser, HasOneCourseTest
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    public $keyType = 'char';
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sub_category_id',
        'title',
        'sub_title',
        'description',
        'is_premium',
        'price',
        'photo',
        'user_id',
        'promotional_price',
        'is_ready'
    ];

    /**
     * Method subCategory
     *
     * @return BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
    /**
     * Get all of the modules for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('step', 'asc');
    }

    /**
     * Get all of the courseReviews for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    /**
     * Get all of the userCourses for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCourses(): HasMany
    {
        return $this->hasMany(UserCourse::class);
    }
    public function currentUserCourse(): HasOne
    {
        return $this->hasOne(UserCourse::class)->where('user_id', auth()->user()->id);
    }
    /**
     * Get the user that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * courseTest
     *
     * @return HasOne
     */
    public function courseTest(): HasOne
    {
        return $this->hasOne(CourseTest::class);
    }
    /**
     * Get all of the courseTests for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseTests(): HasMany
    {
        return $this->hasMany(CourseTest::class);
    }

    /**
     * Get all of the transaction for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
    /**
     * Get all of the courseLearningPaths for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseLearningPaths(): HasMany
    {
        return $this->hasMany(CourseLearningPath::class);
    }
}
