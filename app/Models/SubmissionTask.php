<?php

namespace App\Models;

use App\Models\User;
use App\Models\ModuleTask;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubmissionTask extends Model
{
    use HasFactory, HasUuids;
    public $keyType = 'char';
    public $incrementing = false;
    protected $table = 'submission_tasks';
    protected $primaryKey = 'id';
    protected $fillable = [
        'module_task_id',
        'user_id',
        'answer',
        'file',
        'grade',
    ];

    /**
     * Get the user that owns the SubmissionTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the moduleTask that owns the SubmissionTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moduleTask(): BelongsTo
    {
        return $this->belongsTo(ModuleTask::class);
    }
}
