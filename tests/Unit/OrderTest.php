<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use App\Billing\Charge;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	public function creating_an_order_from_tickets_email_and_charge()
	{
		$charge = new Charge(['amount' => 3600, 'card_last_four' => '1234']);
		$tickets = collect([
			\Mockery::spy(Ticket::class),
			\Mockery::spy(Ticket::class),
			\Mockery::spy(Ticket::class),
		]);

		$order = Order::forTickets($tickets, 'john@example.com', $charge);

		$this->assertEquals('john@example.com', $order->email);
		$this->assertEquals(3600, $order->amount);
		$this->assertEquals('1234', $order->card_last_four);
		$tickets->each->shouldHaveReceived('claimFor', [$order]);
	}

	/** @test */
	public function retrieving_an_order_by_confirmation_number()
	{
		$order = factory(Order::class)->create([
			'confirmation_number' => 'ORDERCONFIRMATION1234',
		]);

		$foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

		$this->assertEquals($order->id, $foundOrder->id);
	}

	/** @test */
	public function retrieving_a_nonexistant_order_by_confirmation_number_throws_an_exception()
	{
		$this->expectException(ModelNotFoundException::class);
		Order::findByConfirmationNumber('NONEXISTENTCONFIRMATIONNUMBER');
	}

	/** @test */
	public function converting_to_an_array()
	{
		$order = factory(Order::class)->create([
			'confirmation_number' => 'ORDERCONFIRMATION1234',
			'email' => 'jane@example.com',
			'amount' => 6000,
		]);
		$order->tickets()->saveMany([
			factory(Ticket::class)->create(['code' => 'TICKETCODE1']),
			factory(Ticket::class)->create(['code' => 'TICKETCODE2']),
			factory(Ticket::class)->create(['code' => 'TICKETCODE3']),
		]);

		$result = $order->toArray();

		$this->assertEquals([
			'confirmation_number' => 'ORDERCONFIRMATION1234',
			'email' => 'jane@example.com',
			'amount' => 6000,
			'tickets' => [
				['code' => 'TICKETCODE1'],
				['code' => 'TICKETCODE2'],
				['code' => 'TICKETCODE3'],
			]
		], $result);
	}
}