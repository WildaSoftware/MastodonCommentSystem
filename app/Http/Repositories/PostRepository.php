<?php

namespace App\Http\Repositories;

use App\Http\Support\Utility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostRepository {

    public function getById($id, $language) {
        return DB::table('post as p')
            ->select([
                'p.*', 'pt.*',
            ])
            ->join('post_translation as pt', 'p.id', '=', 'pt.post_id')
            ->join('language as l', 'pt.language_id', '=', 'l.id')
            ->where(['p.id' => $id])
            ->whereRaw('l.symbol = "'.$language.'"')
            ->first(); 
    }
}