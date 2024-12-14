<?php

namespace Tests\Feature;

use App\Http\Controllers\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JwtEndpointTest extends TestCase
{
  /**
   * A basic feature test example.
   */
  public function test_example(): void
  {
    $ch = curl_init(env("APP_URL") . "/token");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["sub" => "user", "login" => "login@example.com"]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$res = curl_exec($ch);
		curl_close($ch);

    $jwt = json_decode($res);

    $p = JWT::getPayload($jwt->token);

    $this->assertSame("user", $p->sub, "User data in the payload block");
    $this->assertSame("login@example.com", $p->login, "User data in the payload block");
  }
}
