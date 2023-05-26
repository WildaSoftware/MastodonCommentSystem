<div class="col-12 comment {{ $isReply ? 'reply' : '' }}" style="
        margin-left: calc(var(--comment-base-indent) * {{ $comment['depth'] }});
        max-width: calc(100% - (var(--comment-base-indent) * {{ $comment['depth'] }}))
    ">
    <div class="comment-author row">
        <div class="comment-author-avatar col-3 col-md-2 col-lg-1">
            <img src="{{ $comment['account']['avatar_static'] }}" alt="">
        </div>
        <div class="comment-author-details col-9 col-md-7 col-lg-8">
            <div class="row">
                <a class="comment-author-details-name col-12" href="{{ $comment['account']['url'] }}" target="_blank" rel="nofollow">{!! $comment['account']['display_name'] !!}</a>
                <div class="comment-author-details-user col-12">{{ $comment['account']['acct'] }}</div>
            </div>
        </div>
        <div class="comment-date col-12 col-md-3 text-right">
            <a href="{{ $comment['url'] }}" target="_blank" rel="nofollow">{{ $comment['created_at'] }}</a>
        </div>
    </div>
    <div class="comment-content row">
        <div class="col-12">
            {!! $comment['content'] !!}
        </div>
    </div>
    <div class="comment-reply-to row">
        <div class="col-12 text-right">
            <i class="comment-reply-link fa-solid fa-reply fa-lg" data-src="{{ $comment['url'] }}" title="{{ __('replyToThisCommentWithMastodon') }}"></i>
        </div>
    </div>
</div>