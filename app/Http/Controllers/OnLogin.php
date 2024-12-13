<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

		header(sprintf("Authorization: Bearer %s", JWT::create(env("JWT_ALG"), $user, env("JWT_SECRET"))));
  }   
}
