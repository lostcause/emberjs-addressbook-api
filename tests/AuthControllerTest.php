<?php

use App\User;
use Illuminate\Support\Facades\Artisan;

class AuthControllerTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();
		Artisan::call('migrate');
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function test_user_receives_a_token_when_valid_credentials_is_provided()
	{
		$user = factory(User::class)->create(['password' => bcrypt('admin')]);

		$this->post($this->baseUrl.'/auth/login', [
				'email'    => $user->email,
				'password' => 'admin',
			])
			->seeStatusCode(200)
			->seeJsonStructure(['token',])
		;
	}

	public function test_user_receives_an_error_when_invalid_credentials_are_provided()
	{
		$this->post($this->baseUrl.'/auth/login', [
			'email'    => 'foo@bar.com',
			'password' => 'foobar',
		])
		     ->seeStatusCode(401)
		;
	}

	public function test_user_receives_a_token_when_registering_and_email_does_not_exist_in_database()
	{
		$this->post($this->baseUrl.'/auth/register', [
			'email'    => 'foo@bar.com',
			'password' => 'foobar',
		])
			->seeStatusCode(200)
			->seeJsonStructure(['token',])
		;
	}

	public function test_user_receives_an_error_when_registering_and_email_exists_in_database()
	{
		$user = factory(User::class)->create();

		$this
			->post($this->baseUrl.'/auth/register', [
				'email' => $user->email,
				'password' => 'foo',
			])
			->seeStatusCode(409)
			->seeJson([
				'error' => 'User already exists.',
			]);
	}
}
