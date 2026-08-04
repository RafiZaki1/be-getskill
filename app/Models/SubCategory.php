<?php

namespace App\Models;

use App\Base\Interfaces\HasBlogs;
use App\Base\Interfaces\HasCategory;
use App\Base\Interfaces\HasCourses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model implements HasCategory, HasCourses
{
    use HasFactory;

    protected $table = 'sub_categories';
    protected $fillable = ['category_id', 'name'];

    /**
     * Get the category that owns the SubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Get all of the courses for the SubCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

}
