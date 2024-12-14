<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JwtEndpoint extends Controller
{
  public function generate (Request $req): string {
    $authHeader = $req->header('Authorization');

    preg_match('/Bearer\s+(\S+)$/', $authHeader, $m);

    $token = array_key_exists('1', $m) ? $m[1] : '';

    if ($token && !JWT::isExpired($token)) {
      return json_encode([
        'status' => 'valid',
        'message' => 'The token is valid',
        'token' => null,
      ]);
    }

    $d = (array) json_decode($req->getContent());

    $jwt = new JWT();
    $jwt->issue(env('APP_URL'), time(), 3);
    $jwt->create($d);
    
    return json_encode([
      'status' => 'updated',
      'message' => 'The token is updated',
      'token' => $jwt->token,
      'exp' => $jwt->exp,
    ]);
  }

}
