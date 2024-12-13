<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

		header(sprintf("Authorization: Bearer %s", JWT::create(env("JWT_ALG"), $user, env("JWT_SECRET"))));
	}
}	
