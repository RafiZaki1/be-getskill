<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'sub_module_id', 'discussion_id', 'discussion_answer_id', 'faq_id', 'used', 'user_id'];
}
