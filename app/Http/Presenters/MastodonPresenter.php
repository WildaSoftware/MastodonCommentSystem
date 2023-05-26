<?php

namespace App\Http\Presenters;

class MastodonPresenter {

    public function formatGetPostInfoOnInstance(array $input): array {
        $output = $input;

        $output['displayName'] = htmlspecialchars($output['displayName']);
        $output['avatarStatic'] = htmlspecialchars($output['avatarStatic']);

        foreach($output['emojis'] as $emoji) {
            $code = ':'.$emoji['shortcode'];
            $staticUrl = htmlspecialchars($emoji['static_url']);
            $output['displayName'] = str_replace($code, '<img src="'.$staticUrl.'" alt="Emoji '.$code.'" height="20" width="20" />', $comment['account']['display_name']);
        }

        if(!strpos($output['acct'], '@')) {
            $output['acct'] .= '@'.$output['instance'];
        }

        return $output;
    }
}