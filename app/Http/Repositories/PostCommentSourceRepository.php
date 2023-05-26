<?php

namespace App\Http\Repositories;

use App\Models\PostCommentSource;
use Illuminate\Support\Facades\DB;

class PostCommentSourceRepository {

    public function getIdsByPostId($postId, $language) {
        return DB::table('post_comment_source as pcs')
            ->join('language as l', 'pcs.language_id', '=', 'l.id')
            ->whereRaw('l.symbol = "'.$language.'"')
            ->where(['pcs.post_id' => $postId])
            ->pluck('pcs.id');
    }

    public function getByIds($ids) {
        return DB::table('post_comment_source as pcs')
            ->select(['pcs.*'])
            ->whereIn('pcs.id', $ids)
            ->get();
    }

    public function resetCache(int $postId) {
        PostCommentSource::where('post_id', $postId)->update(['data_received' => null]);
    }
}