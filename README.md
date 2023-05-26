# Wilda Software's Mastodon Comment System Example

This simple project presents an approach for a comment system for a blog using Mastodon API in PHP. In this scenario there is one or more "root" toots (posts) on Mastodon and comments are answers for this toot or there toots. The host site (e.g. blog) only downloads toots, presents them in tree view and providing a way to use an user's Mastodon account to post an answer.

It is tested with Mastodon 4.0+ and PHP 7.4 but should also works on PHP 8+.

The whole approach is presented in articles on [the Wilda Software's blog](https://wildasoftware.pl/blog).

# Getting started

Required components:

- PHP and HTTP Server (Apache 2 or NGINX)
- Composer
- Node.js
- MySQL

The exemplary project uses Laravel framework. Follow these steps.

1. Run `composer install` to install vendor libraries, including Laravel.
2. Create an `.env` file in root folder using the `.env.example` file.
3. Create a MySQL database and adjust config in the `.env` file (default name of the database is `ws_mastodon_comment`).
4. Run `php artisan migrate`.
5. Run `php artisan db:seed`.
6. Run `npm install` to install Gulp dependencies.
7. Run `gulp` to compile SCSS scripts into CSS.
8. If you want to use default Laravel's way to run site, run `php artisan serve` and yout base URL is `localhost:8000`. If you prefer to use Apache 2 and localhost, you must add `/public` at the end of base URL.
9. The example post is located on `BASE_URL/post/1`.