<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnLogin extends Controller
{
  public function login (Request $req) {
    $d = json_decode($req->getContent());

		$user = DB::select("SELECT * FROM users WHERE login='$d->login'");
		if (!count($user)) {
			return ["msg" => "login incorrect"];
		}

    $user = $user[0];

    $r = Hash::check($d->pass, $user->password);

    if (!$r) {
			return ["msg" => "Incorrect credentials"];
    }

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
