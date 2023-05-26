<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utility;
use App\Http\Presenters\BlogPresenter;
use App\Http\Repositories\PostRepository;
use App\Http\Repositories\PostCommentSourceRepository;
use App\Models\PostCommentSource;
use App\Models\PostVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class BlogController extends Controller {
    
    private $presenter;
    private $postRepository;
    private $postCommentSourceRepository;

    public function __construct() {
        parent::__construct();

        $this->presenter = new BlogPresenter();
        $this->postRepository = new PostRepository();
        $this->postCommentSourceRepository = new PostCommentSourceRepository();
    }

    public function post(Request $request, $id, $lang = null) {
        $post = $this->postRepository->getById($id, App::currentLocale());

        if(!empty($post)) {			
            $commentSourceIds = $this->postCommentSourceRepository->getIdsByPostId($post->id, App::currentLocale());

            // code after Mastodon authorization
            $mastodonCode = $request->input('code');

            return view('blog-post', [
                'post' => $post, 'commentSourceIds' => $commentSourceIds, 'mastodonCode' => $mastodonCode,
            ]);
        }
        else {
            abort(404);
        }
    }

    public function comments(Request $request) {
        $sourceIds = $request->get('sourceIds', []);
        if(empty($sourceIds)) {
            return response('Missing comment source IDs', 400);
        }

        $commentSources = $this->postCommentSourceRepository->getByIds($sourceIds);
        $mastodonToots = [];

        $now = new \DateTime();

        foreach($commentSources as $source) {
            if(!empty($source->data_received)) {
                $cacheDate = new \DateTime($source->date_data_received);
                $diff = $now->getTimestamp() - $cacheDate->getTimestamp();

                if($diff <= 600) {
                    $mastodonToots[] = json_decode($source->data_received, true);
                    continue;
                }
            }

            try {
                $url = 'http://'.$source->mastodon_instance.'/api/v1/statuses/'.$source->mastodon_toot_id;
                $response = Http::get($url);
                if($response->successful()) {
                    $toot = json_decode($response->body(), true);

                    $url .= '/context';
                    $commentsResponse = Http::get($url);
                    if($commentsResponse->successful()) {
                        $comments = json_decode($commentsResponse->body(), true)['descendants'];
                        $toot['comments'] = $comments;

                        $mastodonToots[] = $toot;
                    }
                    else {
                        Log::error('Error during calling '.$url);
                        Log::error($commentsResponse->status().': '.print_r($commentsResponse->body(), true));
                    }

                    PostCommentSource::where('id', $source->id)->update([
                        'data_received' => json_encode($toot),
                        'date_data_received' => $now->format('Y-m-d H:i:s'),
                    ]);
                }
                else {
                    Log::error('Error during calling '.$url);
                    Log::error($response->status().': '.print_r($response->body(), true));
                }
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }

        return view('inc/blog-post-comments', $this->presenter->formatComments(['mastodonToots' => $mastodonToots]));
    }

    public function commentForm(Request $request) {
        return view('inc/blog-post-comment-form', $request->input());
    }
}