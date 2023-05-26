<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MastodonInstanceCredentials extends Model
{
    use HasFactory;

    protected $table = 'mastodon_instance_credentials';
}
