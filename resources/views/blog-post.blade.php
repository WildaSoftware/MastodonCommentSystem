<!doctype html>
<html lang="<?= App::currentLocale() ?>">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1" />
		<meta http-equiv="Cache-control" content="public">

		<title>{{ __('pageTitle') }}</title>

		<link rel="stylesheet" href="<?= URL::to('/') ?>/bootstrap/css/bootstrap.min.css" type="text/css" />
		<link rel="stylesheet" href="<?= URL::to('/') ?>/fontawesome/css/all.min.css" type="text/css" />
		<link href="<?= URL::to('/') ?>/css/index.min.css" rel="stylesheet" type="text/css" />
	</head>	

    <body>    
        <main role="main">
            <div class="container">
                <div class="background"></div>
                
                <div id="blog-post">
					<div class="container">
						<div class="row">
							<div class="col-12 text-center">
								<h2 class="mt-5 mb-3 h1-to-h2 post-title">{{ $post->title }}</h2>
							</div>
						</div>

                        <div class="row my-4">
                            <div class="col-12 post-content">
                                {!! $post->content !!}
                            </div>
                        </div>

                        @if (count($commentSourceIds) > 0)
                            <div class="row pb-3">
                                <div id="comment-container" class="col-12">
                                    <h2 class="comments-header pb-3">{{ __('comments') }}</h2>

                                    <div id="comment-container-internal">{{ __('loadingComments') }} </div>
                                </div>
                            </div>
                        @endif
					</div>

                    <div class="modal fade" id="mastodon-reply-modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('enterMastodonDomain') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('closeModal') }}">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>{!! __('provideMastodonInstanceToReply') !!}</p>
                                    <input type="hidden" id="mastodon-src-reply"/>
                                    <input type="text" class="form-control" id="mastodon-domain-reply" placeholder="{{ __('provideMastodonInstanceShort') }}">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="mastodon-reply-modal-confirm" class="btn btn-primary">{{ __('confirmModal') }}</button>
                                    <button type="button" id="mastodon-reply-modal-cancel" class="btn btn-secondary" data-dismiss="modal">{{ __('cancelModal') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="mastodon-reply-modal-failure" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('mastodonFailure') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('closeModal') }}">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ __('noMastodonConnectionDescription') }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="mastodon-reply-modal-ok" class="btn btn-primary">{{ __('ok') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
    		            
        </main>
    
    	@include('inc/scripts')
        <script src="<?= URL::to('/') ?>/js/post.js"></script>
		<script>
			(new PostScript(
				"<?= URL::to('/') ?>",
                <?= $post->id ?>,
                <?= json_encode($commentSourceIds) ?>,
                <?= json_encode([
                    'enterMastodonDomain' => __('enterMastodonDomain'),
                ]) ?>,
                <?= !empty($mastodonCode) ? '"'.$mastodonCode.'"' : '' ?>
			)).init();
		</script>
	</body>

</html>
