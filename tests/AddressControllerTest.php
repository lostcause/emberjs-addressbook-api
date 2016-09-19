<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressControllerTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		Artisan::call('migrate');
	}

	public function test_controller_returns_all_addresses_for_a_logged_user_when_calling_index_method()
	{
		$user = factory(App\User::class)->create(['password' => bcrypt('admin')]);

		$token = JWTAuth::fromUser($user);

		$this->refreshApplication();

		$this->get($this->baseUrl.'/addresses', ['HTTP_Authorization' => 'Bearer '.$token])
			 ->seeStatusCode(200);
	}

	public function test_controller_returns_error_for_non_logged_users_when_calling_index_method()
	{
		$this->get($this->baseUrl.'/addresses')
		    ->seeStatusCode(401)
			->seeJson(
				['message' => 'Failed to authenticate because of bad credentials or an invalid authorization header.']
			);
	}

	public function test_controller_creates_an_address_for_a_logged_in_user()
	{
		$user = factory(App\User::class)->create(['password' => bcrypt('admin')]);

		$token = JWTAuth::fromUser($user);

		$this->refreshApplication();

		$data = ['address' => ['name' => 'John Doe', 'email' => 'foo@bar.com']];

		$this->post($this->baseUrl.'/addresses',
				$data,
				['HTTP_Authorization' => 'Bearer '.$token])
		    ->seeStatusCode(200)
			->seeInDatabase('addresses', ['name' => 'John Doe', 'email' => 'foo@bar.com']);
	}

	public function test_controller_returns_error_when_creating_address_for_a_non_logged_user()
	{
		$data = ['address' => ['name' => 'John Doe', 'email' => 'foo@bar.com']];

		$this->post($this->baseUrl.'/addresses', $data)
		     ->seeStatusCode(401)
		     ->notSeeInDatabase('addresses', ['name' => 'John Doe', 'email' => 'foo@bar.com']);
	}

	public function test_controller_updates_an_address_for_a_logged_in_user()
	{
		$user = factory(App\User::class)->create(['password' => bcrypt('admin')]);

		$token = JWTAuth::fromUser($user);

		$this->refreshApplication();

		$address = factory(App\Address::class)->create(['user_id' => $user->id]);

		$this->seeInDatabase('addresses', ['name' => $address->name, 'email' => $address->email]);

		$data = ['address' => ['name' => 'John Doe', 'email' => 'foo@bar.com']];

		$this->put($this->baseUrl.'/addresses/'.$address->id, $data, ['HTTP_Authorization' => 'Bearer '.$token])
		     ->seeStatusCode(200)
		     ->seeInDatabase('addresses', ['name' => 'John Doe', 'email' => 'foo@bar.com']);
	}

	public function test_controller_returns_error_when_updating_an_address_for_a_non_logged_in_user()
	{
		$user = factory(App\User::class)->create(['password' => bcrypt('admin')]);
		$address = factory(App\Address::class)->create(['user_id' => $user->id]);

		$this->seeInDatabase('addresses', ['name' => $address->name, 'email' => $address->email]);

		$data = ['address' => ['name' => 'John Doe', 'email' => 'foo@bar.com']];

		$this->put($this->baseUrl.'/addresses/'.$address->id, $data)
		     ->seeStatusCode(401)
		     ->notSeeInDatabase('addresses', ['name' => 'John Doe', 'email' => 'foo@bar.com']);
	}

	public function test_controller_deletes_an_address_for_a_logged_in_user()
	{
		$user = factory(App\User::class)->create(['password' => bcrypt('admin')]);

		$token = JWTAuth::fromUser($user);

		$this->refreshApplication();

		$address = factory(App\Address::class)->create(['user_id' => $user->id]);

		$this->seeInDatabase('addresses', ['name' => $address->name, 'email' => $address->email]);

		$this->delete($this->baseUrl.'/addresses/'.$address->id, [], ['HTTP_Authorization' => 'Bearer '.$token])
		     ->seeStatusCode(200)
		     ->notSeeInDatabase('addresses', ['name' => 'John Doe', 'email' => 'foo@bar.com']);
	}

	public function test_controller_returns_error_when_deleting_an_address_for_a_non_logged_in_user()
	{
		$user = factory(App\User::class)->create(['password' => bcrypt('admin')]);

		$address = factory(App\Address::class)->create(['user_id' => $user->id]);

		$this->seeInDatabase('addresses', ['name' => $address->name, 'email' => $address->email]);

		$this->delete($this->baseUrl.'/addresses/'.$address->id)
		     ->seeStatusCode(401);
	}
}
