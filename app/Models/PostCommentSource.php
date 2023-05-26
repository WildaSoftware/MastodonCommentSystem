<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCommentSource extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'post_comment_source';
}
