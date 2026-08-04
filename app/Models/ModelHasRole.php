<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelHasRole extends Model
{
    use HasFactory;

    protected $table = 'model_has_roles';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
    ];

    /**
     * Get the role that this model has
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
