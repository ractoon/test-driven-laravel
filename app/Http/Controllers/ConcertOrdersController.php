<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Billing\PaymentGateway;
use App\Billing\PaymentFailedException;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
	private $paymentGateway;

	public function __construct(PaymentGateway $paymentGateway)
	{
		$this->paymentGateway = $paymentGateway;
	}

	public function store($concertId)
	{
		$this->validate(request(), [
			'email' => 'required|email',
			'ticket_quantity' => 'required|integer|min:1',
			'payment_token' => 'required',
		]);

		$concert = Concert::published()->findOrFail($concertId);

		try {
			$reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));
			$order = $reservation->complete($this->paymentGateway, request('payment_token'));
			return response()->json($order, 201);
		} catch (PaymentFailedException $e) {
			$reservation->cancel();
			return response()->json([], 422);
		} catch (NotEnoughTicketsException $e) {
			return response()->json([], 422);
		}
	}
}
