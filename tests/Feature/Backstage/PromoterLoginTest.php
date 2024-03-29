<?php

namespace Tests\Feature;

use Auth;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PromoterLoginTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function logging_in_with_valid_credentials()
    {
    	$this->withoutExceptionHandling();

    	$user = factory(User::class)->create([
    		'email' => 'jane@example.com',
    		'password' => bcrypt('super-secret-password'),
    	]);

    	$response = $this->post('/login', [
    		'email' => 'jane@example.com',
    		'password' => 'super-secret-password',
    	]);

    	$response->assertRedirect('/backstage/concerts');
    	$this->assertTrue(Auth::check());
    	$this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function logging_in_with_invalid_credentials()
    {
    	$this->withoutExceptionHandling();

    	$user = factory(User::class)->create([
    		'email' => 'jane@example.com',
    		'password' => bcrypt('super-secret-password'),
    	]);

    	$response = $this->post('/login', [
    		'email' => 'jane@example.com',
    		'password' => 'not-the-right-password',
    	]);

    	$response->assertRedirect('/login');
    	$response->assertSessionHasErrors('email');
    	$this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_in_with_an_account_that_does_not_exist()
    {
    	$this->withoutExceptionHandling();

    	$response = $this->post('/login', [
    		'email' => 'nobody@example.com',
    		'password' => 'not-the-right-password',
    	]);

    	$response->assertRedirect('/login');
    	$response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    	$this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->create());

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}