<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    public $keyType = 'char';

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * Get all of the modelHasRoles for the Role
     */
    public function modelHasRoles(): HasMany
    {
        return $this->hasMany(ModelHasRole::class);
    }
}
