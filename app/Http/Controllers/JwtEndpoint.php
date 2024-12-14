<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Контроллер для работы с JWT токенами.
 * 
 * Этот класс предоставляет эндпоинт для генерации и обновления JWT токенов,
 * а также проверки их валидности.
 */
class JwtEndpoint extends Controller
{
    /**
   * Генерирует новый JWT токен или обновляет существующий.
   *
   * Проверяет заголовок Authorization на наличие действующего токена. Если токен
   * валиден, возвращает сообщение о его действительности. Если токен отсутствует
   * или истек, создает новый JWT токен и возвращает его.
   *
   * @param Request $req Объект запроса, содержащий данные для создания токена.
   * @return string JSON строка с информацией о статусе и токене.
   */
  public function generate (Request $req): string {
    // Извлечение токена из заголовка Authorization
    $authHeader = $req->header('Authorization');

    preg_match('/Bearer\s+(\S+)$/', $authHeader, $m);

    $token = array_key_exists('1', $m) ? $m[1] : '';

    // Проверка, действителен ли токен
    if ($token && !JWT::isExpired($token)) {
      return json_encode([
        'status' => 'valid',
        'message' => 'The token is valid',
        'token' => null,
      ]);
    }

     // Получение данных для создания нового токена
    $d = (array) json_decode($req->getContent());

    // Создание нового токена
    $jwt = new JWT();
    $jwt->issue(env('APP_URL'), time(), 3);
    $jwt->create($d);
    
    // Возвращаем обновленный токен
    return json_encode([
      'status' => 'updated',
      'message' => 'The token is updated',
      'token' => $jwt->token,
      'exp' => $jwt->exp,
    ]);
  }

}
