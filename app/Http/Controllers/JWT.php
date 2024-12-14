<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JWT extends Controller
{
	public ?string $token = null; 
	private ?string $iss = null;
	private int $iat = 0;
	private int $exp = 0;

	public function create(array $payload, string $alg = "sha256", ?string $secret = ""): void {
		if (!is_null($this->iss)) {
			$payload["iss"] = $this->iss;
			$payload["iat"] = $this->iat ?: time();
			$payload["exp"] = $this->exp ?: ($payload["iat"] + (60 * 3));
		}

		$h = self::base64UrlEncode(json_encode([
			"alg" => $alg,
			"typ" => "JWT",
		]));

		$p = self::base64UrlEncode(json_encode($payload));

		$s = self::base64UrlEncode(hash_hmac($alg, "$h.$p", $secret, true));

		$this->token = sprintf("%s.%s.%s", $h, $p, $s);
	}
	
	public function issue (string $iss, int $iat, int $exp): void {
		$this->iss = $iss;
		$this->iat = $iat;
		$this->exp = $this->iat + (60 * $exp);
	}

	static public function getHeader(string $jwt): object {
		preg_match('/^([^.]+)\./', $jwt, $m);
		return json_decode(self::base64UrlDecode($m[1]));
	}

	static public function getPayload(string $jwt): object {
		preg_match('/\.([^.]+)\./', $jwt, $m);
		return json_decode(self::base64UrlDecode($m[1]));
	}

	static public function getSignature(string $jwt): string {
		preg_match('/([^.]+)$/', $jwt, $m);
		return $m[1];
	}

	static private function base64UrlEncode (string $data): string {
		return str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($data));
	}

	static private function base64UrlDecode (string $data): string {
		return base64_decode(str_replace(["-", "_"], ["+", "/"], $data));
	}

	static public function isExpired (string $jwt) {
		$p = self::getPayload($jwt);
		return ($p->exp ?? 0) > time();
	}

}
