<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnLogin extends Controller
{
  public function login (Request $req) {
    $data = $req->validate([
			'login' => 'required|string',
			'pass' => 'required|string'
		]);

		$user = DB::table('users')->where('login', '=', $data['login'])->first();

		if (!$user || ($user && !Hash::check($data['pass'], $user->password))) {
			return response()->json(['msg' => 'Incorrect credentials'], 401);
		}

		try {

			$client = new Client();
			$res = $client->post(env('APP_URL') . '/token', [
				'headers' => [
					'Authorization' => 'Bearer ' . $req->cookie('JWT_TOKEN'),
				],
				'json' => [
					'sub' => 'user',
					'login' => $data['login'],
				]
			]);

			$jwt = json_decode($res->getBody()->getContents());

			if (!isset($jwt->token)) {
				return response()->json($jwt);
			}

			return response()->json($jwt)->withCookie(Cookie::make('JWT_TOKEN', $jwt->token, 3, '/', null, null, 1));

		} catch (\Exception $e) {

			return response()->json([
				'status' => 'error',
				'message' => $e->getMessage(),
			], 500);

		}

  }   
}
