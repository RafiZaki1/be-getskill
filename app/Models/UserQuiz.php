<?php

namespace App\Models;

use App\Base\Interfaces\HasQuiz;
use App\Base\Interfaces\HasQuizzes;
use App\Base\Interfaces\HasUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuiz extends Model implements HasQuiz, HasUser
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    public $keyType = 'char';
    protected $table = 'user_quizzes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'quiz_id',
        'module_question_id',
        'answer',
        'score',
    ];
    /**
     * Get the user that owns the UserQuiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the quiz that owns the UserQuiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
