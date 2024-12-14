<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnSignup extends Controller
{
	public function signup (Request $req) {
		$d = json_decode($req->getContent());

		$user = DB::select("SELECT * FROM users WHERE login='$d->login'");
		if (count($user)) {
			return ["msg" => "user exists"];
		}

		$bhash = Hash::make($d->pass);

		DB::insert("INSERT INTO users (login, password, created_at, updated_at) VALUES (?, ?, ?, ?)", [$d->login, $bhash, date("Y-m-d H:i:s", time()), date("Y-m-d H:i:s", time())]);

		$user = DB::select("SELECT * FROM users WHERE login='$d->login'");

		$user = $user[0];

		$ch = curl_init(env("APP_URL") . "/token");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer " . $req->cookie("JWT_TOKEN"),
		]); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["sub" => "user", "login" => $user->login]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$res = curl_exec($ch);
		curl_close($ch);

		$jwt = json_decode($res);

		if ($jwt->token) {
			$cookie = Cookie::make("JWT_TOKEN", $jwt->token, 3, "/", null, null, 1);
			return response($res)->withCookie($cookie);
		}

		return response($res);

	}
}	
