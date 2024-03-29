<?php

namespace Test\Feature\Backstage;

use \ConcertFactory;
use App\User;
use App\Concert;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function guests_cannot_view_a_promoters_concert_list()
    {
    	$response = $this->get('/backstage/concerts');

    	$response->assertStatus(302);
    	$response->assertRedirect('/login');
    }

    /** @test */
    public function promoters_can_view_a_list_of_their_concerts()
    {
    	$user = factory(User::class)->create();
    	$otherUser = factory(User::class)->create();
    	$publishedConcertA = ConcertFactory::createPublished(['user_id' => $user->id]);
        $publishedConcertB = ConcertFactory::createPublished(['user_id' => $otherUser->id]);
        $publishedConcertC = ConcertFactory::createPublished(['user_id' => $user->id]);

        $unpublishedConcertA = ConcertFactory::createUnpublished(['user_id' => $user->id]);
        $unpublishedConcertB = ConcertFactory::createUnpublished(['user_id' => $otherUser->id]);
        $unpublishedConcertC = ConcertFactory::createUnpublished(['user_id' => $user->id]);

    	$response = $this->actingAs($user)->get('/backstage/concerts');

    	$response->assertStatus(200);

        $response->data('publishedConcerts')->assertEquals([
            $publishedConcertA,
            $publishedConcertC,
        ]);

        $response->data('unpublishedConcerts')->assertEquals([
            $unpublishedConcertA,
            $unpublishedConcertC,
        ]);
    }
}