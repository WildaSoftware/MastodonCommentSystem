<div class="col-12 comment answer" id="comment-form">
    <div class="row pb-3">
        <div class="col-12 comment-form-header">
            <h3>{{ __('postCommentFormHeader') }}</h3>
        </div>
    </div>
    <div class="comment-author row pb-3">
        <div class="comment-author-avatar col-3 col-md-2 col-lg-1">
            <img src="{{ $avatarStatic }}" alt="">
        </div>
        <div class="comment-author-details col-9 col-md-7 col-lg-8">
            <div class="row">
                <a class="comment-author-details-name col-12" href="{{ $accountUrl }}" target="_blank" rel="nofollow">{!! $displayName !!}</a>
                <div class="comment-author-details-user col-12">{{ $acct }}</div>
            </div>
        </div>
    </div>
    <div class="row pb-3">
        <div class="col-12 comment-form-body">
            <input type="hidden" class="form-control comment-form-postid" value="{{ $postId }}"/>
            <textarea class="form-control comment-form-message" rows="4" placeholder="{{ __('postCommentFormMessage') }}"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-12 comment-form-buttons text-right">
            <button type="button" class="btn btn-primary comment-form-send-button">{{ __('postCommentFormSubmit') }}</button>
        </div>
    </div>
</div>