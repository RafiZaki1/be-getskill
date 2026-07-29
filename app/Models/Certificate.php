<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_course_id',
        'user_event_id',
        'code',
        'username',
        'certificate',
    ];
    /**
     * Get the userCourse that owns the Certificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCourse(): BelongsTo
    {
        return $this->belongsTo(UserCourse::class);
    }

    /**
     * Get the userEvent that owns the Certificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userEvent(): BelongsTo
    {
        return $this->belongsTo(UserEvent::class);
    }
}
