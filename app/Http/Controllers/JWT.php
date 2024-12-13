<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JWT extends Controller
{
	static public function create($alg, $data, $secret) :string {
		$h = base64_encode(json_encode([
			"alg" => $alg,
			"typ" => "JWT",
		]));

		$p = base64_encode(json_encode($data));

		$s = base64_encode(Hash($alg, $secret));

		return sprintf("%s.%s.%s", $h, $p, $s);
	}
	
	static public function getHeader($jwt) {
		preg_match('/^([^.]+)\./', $jwt, $m);
		return $m[1];
	}

	static public function getPayload($jwt) {
		preg_match('/\.([^.]+)\./', $jwt, $m);
		return $m[1];
	}

	static public function getSecret($jwt) {
		preg_match('/([^.]+)$/', $jwt, $m);
		return $m[1];
	}
}
