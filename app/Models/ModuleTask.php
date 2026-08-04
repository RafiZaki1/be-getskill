<?php

namespace App\Models;

use App\Models\User;
use App\Models\Module;
use App\Models\SubmissionTask;
use App\Base\Interfaces\HasModule;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuleTask extends Model implements HasModule
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    public $keyType = 'char';
    protected $table = 'module_tasks';
    protected $primaryKey = 'id';
    protected $fillable = [
        'module_id',
        'user_id',
        'step',
        'question',
        'point',
        'description',
        'answer'
    ];

    /**
     * Get the module that owns the ModuleTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get all of the submissionTask for the ModuleTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissionTask(): HasMany
    {
        return $this->hasMany(SubmissionTask::class);
    }

    /**
     * Get the user that owns the ModuleTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
