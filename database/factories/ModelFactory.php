<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = '$2y$10$fU1mA/e1VmXYuuEdTOfzr.Y.XrYqk7DaRVp5OlSeNhd09ZlvVESxu',
        'remember_token' => str_random(10),
        'stripe_account_id' => 'test_acct_1234',
        'stripe_access_token' => 'test_token',
    ];
});

$factory->define(App\Concert::class, function (Faker\Generator $faker) {
	return [
		'user_id' => function() {
			return factory(App\User::class)->create()->id;
		},
		'title' => 'Example Band',
		'subtitle' => 'with The Fake Openers',
		'additional_information' => 'Some sample additional information.',
		'date' => Carbon::parse('+2 weeks'),
		'venue' => 'The Example Theater',
		'venue_address' => '123 Example Lane',
		'city' => 'Fakeville',
		'state' => 'ON',
		'zip' => '90210',
		'ticket_price' => 2000,
		'ticket_quantity' => 5,
	];
});

$factory->state(App\Concert::class, 'published', function (Faker\Generator $faker) {
	return [
		'published_at' => Carbon::parse('-1 week'),
	];
});

$factory->state(App\Concert::class, 'unpublished', function (Faker\Generator $faker) {
	return [
		'published_at' => null,
	];
});

$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
	return [
		'concert_id' => function() {
			return factory(App\Concert::class)->create()->id;
		},
	];
});

$factory->state(App\Ticket::class, 'reserved', function (Faker\Generator $faker) {
	return [
		'reserved_at' => Carbon::now(),
	];
});

$factory->define(App\Order::class, function (Faker\Generator $faker) {
	return [
		'amount' => 5250,
		'email' => 'somebody@example.com',
		'confirmation_number' => 'ORDERCONFIRMATION1234',
		'card_last_four' => '1234',
	];
});

$factory->define(App\Invitation::class, function (Faker\Generator $faker) {
	return [
		'email' => 'somebody@example.com',
		'code' => 'TESTCODE1234',
	];
});