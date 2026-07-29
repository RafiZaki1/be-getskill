<?php

namespace App\Models;

use App\Base\Interfaces\HasModule;
use App\Base\Interfaces\HasUserQuizzes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model implements HasModule, HasUserQuizzes
{
    use HasFactory, HasUuids;
    public $keyType = 'char';
    public $incrementing = false;
    protected $table = 'quizzes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'module_id',
        'rules',
        'retry_delay',
        'minimum_score',
        'rules',
        'total_question',
        'duration',
        'is_submitted',
    ];
    /**
     * Get the module that owns the Quiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
    /**
     * Get all of the userQuizzes for the Quiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userQuizzes(): HasMany
    {
        return $this->hasMany(UserQuiz::class);
    }
}
