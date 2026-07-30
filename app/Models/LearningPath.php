<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningPath extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    public $keyType = 'char';

    protected $table = 'learning_paths';

    protected $fillable = [
        'title',
        'division_id',
        'class_level',
        'slug',
        'description',
    ];

    /**
     * Get all of the courseLearningPaths for the LearningPath
     */
    public function courseLearningPaths(): HasMany
    {
        return $this->hasMany(CourseLearningPath::class);
    }
}
