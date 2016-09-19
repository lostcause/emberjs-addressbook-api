<?php

namespace App\Http\Controllers;

use App\Address;
use App\Http\Requests;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressController extends Controller
{
	public function index()
	{
		return response()->json(['address' => JWTAuth::parseToken()->toUser()->addresses()->get()]);
	}

	public function save(Request $request)
	{

		$address = new Address;
		$address->name = $request->get('address')['name'];
		$address->email = $request->get('address')['email'];

		JWTAuth::parseToken()->toUser()->addresses()->save($address);

		return response()->json(['address' => $address]);
	}

	public function edit($id)
	{
		$address = JWTAuth::parseToken()->toUser()->addresses()->find($id);
		if(!is_null($address) && $address->count() > 0)
			return response()->json(['address' => $address]);
		else
			return response()->json([]);
	}

	public function update(Request $request, $id)
	{
		$address = JWTAuth::parseToken()->toUser()->addresses()->find($id);

		$address->name = $request->get('address')['name'];
		$address->email = $request->get('address')['email'];

		$address->save();

		return response()->json(['address' => $address]);
	}

	public function delete($id)
	{
		$address = Address::find($id);
		$address->destroy($id);

		return response()->json(array());
	}
}
