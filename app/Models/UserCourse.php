<?php

namespace App\Models;

use App\Base\Interfaces\HasCourse;
use App\Base\Interfaces\HasSubModule;
use App\Base\Interfaces\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserCourse extends Model implements HasUser, HasCourse, HasSubModule
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'sub_module_id', 'has_pre_test', 'has_post_test', 'has_downloaded'];

    /**
     * Get the user that owns the UserCourse
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the course that owns the UserCourse
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    /**
     * Get the subModule that owns the UserCourse
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subModule(): BelongsTo
    {
        return $this->belongsTo(SubModule::class);
    }
    /**
     * Get the certificate associated with the UserCourse
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
