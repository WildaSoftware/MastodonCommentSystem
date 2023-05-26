<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;
use App\Models\Language;

class CreateBasicTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 5);
        });

        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('post_translation', function (Blueprint $table) {
            $table->foreignIdFor(Post::class);
            $table->foreignIdFor(Language::class);
            $table->string('title', 1024);
            $table->longText('content');
            $table->timestamps();

            $table->primary(['post_id', 'language_id']);
            $table->foreign('post_id')->references('id')->on('post');
            $table->foreign('language_id')->references('id')->on('language');
        });
		
		Schema::create('post_comment_source', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Post::class);
            $table->foreignIdFor(Language::class);
            $table->string('mastodon_instance', 128);
            $table->string('mastodon_user', 128);
            $table->string('mastodon_toot_id', 128);
			$table->mediumText('data_received')->nullable();
            $table->timestampTz('date_data_received')->nullable();

            $table->foreign('post_id')->references('id')->on('post');
            $table->foreign('language_id')->references('id')->on('language');
        });
		
		Schema::create('mastodon_instance_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('instance', 512)->unique();
            $table->string('client_id', 1024)->nullable();
            $table->string('client_secret', 1024)->nullable();
            $table->string('vapid_key', 1024)->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('mastodon_instance_credentials');
		Schema::dropIfExists('post_comment_source');
        Schema::dropIfExists('post_translation');
        Schema::dropIfExists('post');
        Schema::dropIfExists('language');
    }
}
