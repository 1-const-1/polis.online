<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class JwtEndpoint extends Controller
{
  public function generate (Request $req): string {
    preg_match("/Bearer(.*)/", $req->header("Authorization"), $m);
    $token = count($m) > 1 ? trim($m[1]) : "";

    if ($token && JWT::isExpired($token)) {
      return json_encode([
        "status" => "valid",
        "message" => "The token is valid",
        "token" => null,
      ]);
    }

    $d = (array) json_decode($req->getContent());

    $jwt = new JWT();
    $jwt->issue(env("APP_URL"), time(), 3);
    $jwt->create($d);

    header("Content-Type: application/json");
    
    return json_encode([
      "status" => "updated",
      "message" => "The token is updated",
      "token" => $jwt->token
    ]);
  }

}
