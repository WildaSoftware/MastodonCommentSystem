<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('language')->insert([
			'id' => 1,
            'symbol' => 'en',
        ]);
		
		DB::table('post')->insert([
			'id' => 1,
		]);
		
		DB::table('post_translation')->insert([
			'post_id' => 1,
			'language_id' => 1,
			'title' => 'Meet Wilda Software!',
			'content' => '<p>Formally, we have been active since 2018, but our common history goes back even more than a dozen years earlier, when some members of the team started cooperating with each other on academic projects. It quickly turned out that this work not only went well, but also brought measurable results, so it was continued, both in the university field and commercially. Since then, project managers and individual members of the technical department gained experience while creating IT projects for many clients. Software that was created during this time was very diverse in nature - they were large web applications, convenient to use computer programs for mobile devices, desktop solutions for Windows or algorithms helping to optimize production. Moreover, during this time we have consulted and documented many IT systems of different sizes, domain and levels of advancement, which has toughened us and made us able to understand the technological expectations of each client.</p><p>Our team consists of specialists in IT project management and programmers - engineers who deal with both the visible (front-end) and invisible (back-end) side of the application. We also work closely with professionals dealing with server administration, graphic design and search engine optimization, so we can offer a full package of services.</p><p>In our team we focus on improving each other\'s skills - people experienced in specific technical aspects are willing to share their knowledge with other members of the team, thus raising the general awareness of different techniques and making the technical department as flexible as possible. Such development is also possible thanks to the fact that the team consists of people who not only have IT knowledge, but also have teaching skills, which are used in conducting laboratory classes and lecturing at various universities.</p><p>Projects are supervised by experienced people with strong analytical skills who can understand and reformulate the client\'s challenge to successfully create the desired IT system. Project managers are able to organize the team\'s and their own work, always finding time and understanding the changing needs of clients.</p><p>We understand that you may not believe the above description.<br />That\'s why we invite you to <a href="https://wildasoftware.pl/#contact">contact us</a> and get to know each other!</p>',
		]);
		
		DB::table('post_comment_source')->insert([
			'id' => 1,
			'post_id' => 1,
			'language_id' => 1,
			'mastodon_instance' => 'social.wildasoftware.pl',
			'mastodon_user' => 'wilda',
			'mastodon_toot_id' => '110435606334801439',
		]);
    }
}
