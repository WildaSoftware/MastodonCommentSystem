<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utility;
use App\Http\Presenters\BlogPresenter;
use App\Http\Presenters\MastodonPresenter;
use App\Http\Repositories\MastodonInstanceCredentialsRepository;
use App\Http\Repositories\PostAttachmentRepository;
use App\Http\Repositories\PostCommentSourceRepository;
use App\Http\Repositories\PostLikeRepository;
use App\Http\Repositories\PostRepository;
use App\Http\Repositories\PostVisitRepository;
use App\Http\Repositories\TagRepository;
use App\Models\MastodonInstanceCredentials;
use App\Models\PostVisit;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class MastodonController extends Controller {

    private $presenter;
    private $mastodonInstanceCredentialsRepository;
    private $postCommentSourceRepository;

    public function __construct() {
        parent::__construct();

        $this->presenter = new MastodonPresenter();
        $this->mastodonInstanceCredentialsRepository = new MastodonInstanceCredentialsRepository();
        $this->postCommentSourceRepository = new PostCommentSourceRepository();
    }

    public function getAppCredentials(Request $request, $instance) {
        $redirectUri = $request->get('redirectUri', env('MASTODON_APP_REDIRECT_URI'));
        if(App::environment('local')) {
            $redirectUri = env('MASTODON_APP_REDIRECT_URI');
        }

        $credentials = $this->mastodonInstanceCredentialsRepository->getByInstance($instance);
        if(empty($credentials)) {
            $url = 'https://'.$instance.'/api/v1/apps';
            $body = [
                'client_name' => env('MASTODON_APP_NAME'),
                'redirect_uris' => $redirectUri,
                'scopes' => str_replace('+', ' ', env('MASTODON_APP_SCOPES')),
                'website' => URL::to('/'),
            ];
            $response = Http::asForm()->post($url, $body);

            if($response->successful()) {
                $rawOutput = $response->body();
                $output = json_decode($response->body(), true);
                $now = (new DateTime())->format('Y-m-d H:i:s');

                $credentials = new MastodonInstanceCredentials();
                $credentials->instance = $instance;
                $credentials->client_id = $output['client_id'];
                $credentials->client_secret = $output['client_secret'];
                $credentials->vapid_key = $output['vapid_key'];
                $credentials->response = $rawOutput;
                $credentials->save();
            }
            else {
                $message = 'Error during calling '.$url.' with body '.print_r($body, true);
                Log::error($message);
                Log::error($response->status().': '.print_r($response->body(), true));
                return response(json_encode(['message' => $message], 500));
            }
        }

        $response = [
            'clientId' => $credentials->client_id,
            'redirectUri' => $redirectUri,
            'scope' => env('MASTODON_APP_SCOPES'),
        ];

        return response(json_encode($response), 200);
    }

    public function getToken(Request $request, $instance, $code) {
        $redirectUri = $request->get('redirectUri', env('MASTODON_APP_REDIRECT_URI'));
        if(App::environment('local')) {
            $redirectUri = env('MASTODON_APP_REDIRECT_URI');
        }

        $credentials = $this->mastodonInstanceCredentialsRepository->getByInstance($instance);
        $accessToken = null;
        $createdAt = null;
        if(!empty($credentials)) {
            $url = 'https://'.$instance.'/oauth/token';
            $body = [
                'client_id' => $credentials->client_id,
                'client_secret' => $credentials->client_secret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
	            'code' => $code,
                'scope' => str_replace('+', ' ', env('MASTODON_APP_SCOPES')),
            ];
            $response = Http::asForm()->post($url, $body);

            if($response->successful()) {
                $body = json_decode($response->body(), true);
                $accessToken = $body['access_token'];
                $createdAt = $body['created_at'];
            }
            else {
                $message = 'Error during calling '.$url.' with body '.print_r($body, true);
                Log::error($message);
                Log::error($response->status().': '.print_r($response->body(), true));
                return response(json_encode(['message' => $message], 500));
            }
        }
        else {
            $message = 'mastodon/getToken fail - there is no instance like '.$instance;
            Log::error($message);
            return response(json_encode(['message' => $message], 500));
        }

        $response = [
            'accessToken' => $accessToken,
            'createdAt' => $createdAt,
        ];

        return response(json_encode($response), 200);
    }

    public function getPostInfoOnInstance(Request $request, $instance) {
        $postUrl = $request->input('postUrl');
        $accessToken = $request->input('accessToken');

        $output = [
            'postId' => null,
            'instance' => $instance,
            'acct' => null,
            'avatarStatic' => null,
            'displayName' => null,
            'emojis' => null,
            'accountUrl' => null,
        ];

        $url = 'https://'.$instance.'/api/v2/search';
        $params = [
            'q' => $postUrl,
            'type' => 'statuses',
            'resolve' => true,
            'limit' => 1,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ])->get($url, $params);

        if($response->successful()) {
            $body = json_decode($response->body(), true);
            if(!empty($body['statuses'])) {
                $output['postId'] = $body['statuses'][0]['id'];
            }
            else {
                $message = 'mastodon/getPostIdOnInstance fail - there is no post '.$url.' on instance '.$instance;
                Log::error($message);
                return response(json_encode(['message' => $message], 500));
            }
        }
        else {
            $message = 'Error during calling '.$url.' with body '.print_r($params, true);
            Log::error($message);
            Log::error($response->status().': '.print_r($response->body(), true));
            return response(json_encode(['message' => $message], 500));
        }

        //--

        $urlAccount = 'https://'.$instance.'/api/v1/accounts/verify_credentials';

        $responseAccount = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ])->get($urlAccount);

        if($responseAccount->successful()) {
            $body = json_decode($responseAccount->body(), true);

            $output['acct'] = $body['acct'];
            $output['displayName'] = $body['display_name'];
            $output['avatarStatic'] = $body['avatar_static'];
            $output['emojis'] = $body['emojis'];
            $output['accountUrl'] = $body['url'];
        }
        else {
            $message = 'Error during calling '.$url;
            Log::error($message);
            Log::error($responseAccount->status().': '.print_r($responseAccount->body(), true));
            return response(json_encode(['message' => $message], 500));
        }

        return response(json_encode($this->presenter->formatGetPostInfoOnInstance($output)), 200);
    }

    public function sendToot(Request $request, $instance) {
        $message = $request->post('message');
        $answeredTootId = $request->post('answeredTootId');
        $accessToken = $request->post('accessToken');
        $postId = $request->post('postId');

        $url = 'https://'.$instance.'/api/v1/statuses';
        $params = [
            'status' => $message,
            'in_reply_to_id' => $answeredTootId,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ])->post($url, $params);

        if($response->successful()) {
            $this->postCommentSourceRepository->resetCache($postId);
        }
        else {
            $message = 'Error during calling '.$url.' with body '.print_r($params, true);
            Log::error($message);
            Log::error($response->status().': '.print_r($response->body(), true));
            return response(json_encode(['message' => $message], 500));
        }

        return response('', 200);
    }
}