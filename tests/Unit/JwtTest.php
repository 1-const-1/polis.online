<?php

namespace Tests\Unit;

use App\Http\Controllers\JWT;
use PHPUnit\Framework\TestCase;

class JwtTest extends TestCase
{
  /**
   * JWT testing
   */
  public function test_example(): void
  {
    $jwt = new JWT();
    $jwt->issue("issuer.com", time(), 3);
    $jwt->create(["sub" => "user_data"]);

    $this->assertSame("user_data", JWT::getPayload($jwt->token)->sub, "User data in the payload block");
  }
}
