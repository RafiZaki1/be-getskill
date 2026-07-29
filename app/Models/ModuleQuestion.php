<?php

namespace App\Models;

use App\Base\Interfaces\HasModule;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleQuestion extends Model implements HasModule
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    public $keyType = 'char';
    protected $table = 'module_questions';
    protected $primaryKey = "id";
    protected $fillable = [
        'module_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'answer',
    ];
    /**
     * Get the module that owns the ModuleQuestion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
