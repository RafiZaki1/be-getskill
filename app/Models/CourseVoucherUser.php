<?php

namespace App\Models;

use App\Base\Interfaces\HasCourseVoucher;
use App\Base\Interfaces\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseVoucherUser extends Model implements HasUser, HasCourseVoucher
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'course_voucher_id'
    ];
    /**
     * Get the user that owns the CourseVoucherUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'foreign_key', 'other_key');
    }
    /**
     * Get the courseVoucher that owns the CourseVouchercourseVoucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseVoucher(): BelongsTo
    {
        return $this->belongsTo(CourseVoucher::class);
    }
}
