<?php

namespace App\Models;

use App\Base\Interfaces\HasModule;
use App\Base\Interfaces\HasUserCourse;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubModule extends Model implements HasModule, HasUserCourse
{
    use HasFactory, HasUuids;
    public $keyType = 'char';
    public $incrementing = false;
    protected $table = 'sub_modules';
    protected $fillable = [
        'module_id',
        'title',
        'slug',
        'step',
        'sub_title',
        'content',
    ];
    /**
     * Get the module that owns the SubModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
    /**
     * Get the userCourse that owns the SubModule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCourse(): BelongsTo
    {
        return $this->belongsTo(UserCourse::class);
    }
}
