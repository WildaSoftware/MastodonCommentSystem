class PostScript {

    constructor(baseUrl, postId, commentSourceIds, translations, mastodonCode) {
        this.baseUrl = baseUrl + '/';
        this.postId = postId;
        this.commentSourceIds = commentSourceIds;
        this.translations = translations;
        this.mastodonCode = mastodonCode;

        this.isCommentContainerLoaded = false;
    }

    init() {
        $('#comment-container').on('click', '.comment-reply-link', (event) => {
            const savedDomain = localStorage.getItem('lastMastodonDomain');
            if(savedDomain) {
                $('#mastodon-domain-reply').val(savedDomain);
            }
            
            const src = $(event.target).data('src');
            $('#mastodon-src-reply').val(src);

            $('#mastodon-domain-reply').removeClass('error');
            $('#mastodon-reply-modal').modal('show');
        });

        $('#mastodon-reply-modal-confirm').on('click', (event) => {
            $('#mastodon-domain-reply').removeClass('error');
            
            const domain = $('#mastodon-domain-reply').val();
            const src = $('#mastodon-src-reply').val();
            if(domain && src) {
                $('#mastodon-reply-modal').modal('hide');
                localStorage.setItem('lastMastodonDomain', domain);
                localStorage.setItem('lastMastodonSrc', src);

                $.ajax({
                    type: 'GET',
                    url: this.baseUrl + 'mastodon/app/' + domain,
                    data: { redirectUri: window.location.href },
                    cache: true,
                    success: (res) => {
                        const json = JSON.parse(res);
    
                        const authorizeUrl = 'https://' + domain + '/oauth/authorize?' 
                            + 'client_id=' + json.clientId 
                            + '&scope=' + json.scope
                            + '&redirect_uri=' + json.redirectUri
                            + '&response_type=code';
                        window.location.href = authorizeUrl;
                    },
                    error: (err) => {
                        $('#mastodon-reply-modal-failure').modal('show');
                    }
                });
            }
            else {
                $('#mastodon-domain-reply').addClass('error');
            }
        });

        if(this.commentSourceIds.length > 0 && !this.mastodonCode) {
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if(entry.intersectionRatio > 0) {
                        this.loadComments();
                    }
                });
            }, { root: null });
          
            observer.observe(document.getElementById('comment-container'));
        }

        if(this.mastodonCode) {
            if(this.commentSourceIds.length > 0) {
                this.loadComments(true);
            }
        }

        $('#comment-container').on('click', '.comment-form-send-button', (event) => {
            const answer = $(event.target).closest('.answer');
            const answeredTootId = answer.find('.comment-form-postid').val();
            const message = answer.find('.comment-form-message').val();

            const instance = localStorage.getItem('lastMastodonDomain');
            const accessToken = localStorage.getItem('mastodonAccessToken');

            if(message) {
                $.ajax({
                    type: 'POST',
                    url: this.baseUrl + 'mastodon/toot/' + instance,
                    data: { message: message, answeredTootId: answeredTootId, accessToken: accessToken, postId: this.postId },
                    cache: false,
                    success: (res) => {
                        answer.remove();
                        this.isCommentContainerLoaded = false;
                        this.loadComments();
                    },
                    error: (err) => {
                        $('#mastodon-reply-modal-failure').modal('show');
                    }
                });
            }
        });

        $('#mastodon-reply-modal-ok').click((event) => {
            $('#mastodon-reply-modal-failure').modal('hide');
        });
    }
    
    loadComments(withFormInitiating = false) {
        if(this.isCommentContainerLoaded) {
            return;
        }

        $.get(this.baseUrl + 'post/comment', { sourceIds: this.commentSourceIds }, (page) => {
            $('#comment-container-internal').html(page);
            this.isCommentContainerLoaded = true;

            if(withFormInitiating) {
                const instance = localStorage.getItem('lastMastodonDomain');
                const postSrc = localStorage.getItem('lastMastodonSrc');

                let accessToken = localStorage.getItem('mastodonAccessToken');
                let accessTokenCreatedBy = localStorage.getItem('mastodonCreatedBy');
                const accessTokenLifespan = 4 * 3600 * 1000;

                if(!accessToken || !accessTokenCreatedBy || instance != localStorage.getItem('mastodonAccessTokenInstance')
                    || (Date.now - accessTokenLifespan > accessTokenCreatedBy)        
                ) {
                    $.ajax({
                        type: 'GET',
                        url: this.baseUrl + 'mastodon/token/' + instance + '/' + this.mastodonCode,
                        data: { redirectUri: window.location.href },
                        cache: false,
                        success: (res) => {
                            const json = JSON.parse(res);
                            accessToken = json.accessToken;
                            accessTokenCreatedBy = json.createdBy;
                            localStorage.setItem('mastodonAccessToken', accessToken);
                            localStorage.setItem('mastodonCreatedBy', Date.now());
                            localStorage.setItem('mastodonAccessTokenInstance', instance);

                            this.prepareCommentBox(instance, postSrc, accessToken);
                        },
                        error: (err) => {
                            $('#mastodon-reply-modal-failure').modal('show');
                        }
                    });                
                }
                else {
                    this.prepareCommentBox(instance, postSrc, accessToken);
                }
            }
        });
    }

    prepareCommentBox(instance, postSrc, accessToken) {
        $.ajax({
            type: 'GET',
            url: this.baseUrl + 'mastodon/post/' + instance,
            data: { postUrl: postSrc, accessToken},
            cache: false,
            success: (resPost) => {
                const postJson = JSON.parse(resPost);
                
                $.get(this.baseUrl + 'post/comment/form', postJson, (form) => {
                    $('#comment-form').remove();
                    const commentBox = $('.comment-reply-link[data-src="' + postSrc + '"]').closest('.comment');
                    commentBox.after(form);

                    document.querySelector('#comment-form').scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            },
            error: (errPost) => {
                $('#mastodon-reply-modal-failure').modal('show');
            }
        });
    }
}

