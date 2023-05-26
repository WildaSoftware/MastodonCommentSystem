<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class MastodonInstanceCredentialsRepository {

    public function getByInstance(string $instance) {
        return DB::table('mastodon_instance_credentials as mic')
            ->where(['mic.instance' => $instance])
            ->first();
    }
}