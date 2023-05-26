@if (count($mastodonToots) > 0)
    @foreach($mastodonToots as $toot)
        @include('inc/blog-post-comment-box', ['comment' => $toot, 'isReply' => false])

        @foreach($toot['comments'] as $reply)
            @include('inc/blog-post-comment-box', ['comment' => $reply, 'isReply' => true])
        @endforeach
    @endforeach
@else
    {{ __('noComments') }}
@endif