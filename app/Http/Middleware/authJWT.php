<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use JWTAuth;

class authJWT

{

	public function handle($request, Closure $next)
	{

		try
		{
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}
		}
		catch(Exception $e)
		{

			if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
			{

				return response()->json(['error' => 'Token is Invalid']);
			}else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
			{

				return response()->json(['error' => 'Token is Expired']);
			}else
			{

				return response()->json(['error' => $e->getMessage()]);
			}
		}

		return $next($request);
	}

}