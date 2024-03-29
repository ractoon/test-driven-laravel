<?php

namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function user_can_view_their_order_confirmation()
    {
    	$this->withoutExceptionHandling();

    	$concert = factory(Concert::class)->states('published')->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('January 13, 2019 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.',
        ]);
    	$order = factory(Order::class)->create([
    		'confirmation_number' => 'ORDERCONFIRMATION1234',
    		'card_last_four' => '1881',
    		'amount' => 8500,
    		'email' => 'john@example.com',
    	]);
    	$ticketA = factory(Ticket::class)->create([
    		'concert_id' => $concert->id,
    		'order_id' => $order->id,
    		'code' => 'TICKETCODE123',
    	]);
    	$ticketA = factory(Ticket::class)->create([
    		'concert_id' => $concert->id,
    		'order_id' => $order->id,
    		'code' => 'TICKETCODE456',
    	]);

    	$response = $this->get('/orders/ORDERCONFIRMATION1234');

    	$response->assertStatus(200);

    	$response->assertViewHas('order', function ($viewOrder) use ($order) {
    		return $order->id === $viewOrder->id;
    	});

    	$response->assertSee('ORDERCONFIRMATION1234');
    	$response->assertSee('$85.00');
    	$response->assertSee('**** **** **** 1881');
    	$response->assertSee('TICKETCODE123');
    	$response->assertSee('TICKETCODE456');
    	$response->assertSee('The Red Chord');
    	$response->assertSee('with Animosity and Lethargy');
    	$response->assertSee('The Mosh Pit');
    	$response->assertSee('123 Example Lane');
    	$response->assertSee('Laraville, ON');
    	$response->assertSee('17916');
    	$response->assertSee('john@example.com');

    	$response->assertSee('2019-01-13 20:00');
    }
}