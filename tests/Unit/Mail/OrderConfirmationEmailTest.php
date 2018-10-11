<?php

use App\Order;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function email_contains_a_link_to_the_order_confirmation_page()
    {
    	$order = factory(Order::class)->make([
    		'confirmation_number' => 'ORDERCONFIRMATION1234',
    	]);
    	$email = new OrderConfirmationEmail($order);

    	
    }
}