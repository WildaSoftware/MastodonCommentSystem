<?php

namespace App\Http\Presenters;

use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class BlogPresenter {

    public function formatComments(array $input): array {
        $output = $input;

        $replyCount = [];

        foreach($output['mastodonToots'] as &$toot) {
            $toot = $this->presentComment($toot);
            if(!empty($toot['in_reply_to_id'])) {
                if(!array_key_exists($toot['in_reply_to_id'], $replyCount)) {
                    $replyCount[$toot['in_reply_to_id']] = 0;
                }
                $replyCount[$toot['in_reply_to_id']]++;
            }

            foreach($toot['comments'] as &$reply) {
                $reply = $this->presentComment($reply);
                if(!empty($reply['in_reply_to_id'])) {
                    if(!array_key_exists($reply['in_reply_to_id'], $replyCount)) {
                        $replyCount[$reply['in_reply_to_id']] = 0;
                    }
                    $replyCount[$reply['in_reply_to_id']]++;
                }
            }
        }

        $depths = [];
        $isIncreasingChildrenDepth = [];
        
        // setting an appropriate toot's depth in terms of responded post
        foreach($output['mastodonToots'] as &$toot) {
            $isIncreasingChildrenDepth[$toot['id']] = false;

            if(empty($toot['in_reply_to_id'])) {
                $depth = 0;
                $toot['depth'] = $depth;
                $depths[$toot['id']] = $depth;
            }
            else {
                $depth = ($depths[$toot['in_reply_to_id']] ?? 0)
                    + (!empty($isIncreasingChildrenDepth[$toot['in_reply_to_id']]) ? 1 : 0);
                $toot['depth'] = $depth;
                $depths[$toot['id']] = $depth;

                if($replyCount[$toot['in_reply_to_id']] > 1) {
                    $isIncreasingChildrenDepth[$toot['id']] = true;
                }
            }

            foreach($toot['comments'] as &$reply) {
                $isIncreasingChildrenDepth[$reply['id']] = false;

                $depth = ($depths[$reply['in_reply_to_id']] ?: 1)
                    + (!empty($isIncreasingChildrenDepth[$reply['in_reply_to_id']]) ? 1 : 0);
                $reply['depth'] = $depth;
                $depths[$reply['id']] = $depth;

                if($replyCount[$reply['in_reply_to_id']] > 1) {
                    $isIncreasingChildrenDepth[$reply['id']] = true;
                }
            }
        }

        return $output;
    }

    private function presentComment(array $comment) {
        $output = $comment;
        $output['account']['display_name'] = htmlspecialchars($output['account']['display_name']);
        $output['account']['avatar_static'] = htmlspecialchars($output['account']['avatar_static']);

        foreach($output['account']['emojis'] as $emoji) {
            $code = ':'.$emoji['shortcode'];
            $staticUrl = htmlspecialchars($emoji['static_url']);
            $output['account']['display_name'] = str_replace($code, '<img src="'.$staticUrl.'" alt="Emoji '.$code.'" height="20" width="20" />', $comment['account']['display_name']);
        }

        if(!strpos($output['account']['acct'], '@')) {
            $parsedUrl = parse_url($output['account']['url']);
            if(!empty($parsedUrl)) {
                $output['account']['acct'] .= '@'.$parsedUrl['host'];
            }
        }

        $output['created_at'] = (new DateTime($output['created_at']))->format('d.m.Y H:i:s');

        return $output;
    }
}