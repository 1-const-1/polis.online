<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Контроллер для обработки процесса аутентификации пользователя.
 * 
 * Этот класс предоставляет метод для авторизации пользователя с использованием
 * логина и пароля, а также для получения и хранения JWT токена.
 */
class OnLogin extends Controller
{
	/**
	 * Авторизация пользователя и получение JWT токена.
	 *
	 * Проверяет введенные логин и пароль, если данные корректны, генерирует запрос
	 * к внешнему сервису для получения JWT токена, и возвращает его вместе с cookie.
	 *
	 * @param Request $req Объект запроса, содержащий данные для аутентификации.
	 * @return \Illuminate\Http\JsonResponse Ответ с токеном или ошибкой.
	 */
  public function login (Request $req) {
		 // Валидация входных данных
    $data = $req->validate([
			'login' => 'required|string',
			'pass' => 'required|string'
		]);

		// Поиск пользователя в базе данных
		$user = DB::table('users')->where('login', '=', $data['login'])->first();

		// Проверка правильности логина и пароля
		if (!$user || ($user && !Hash::check($data['pass'], $user->password))) {
			return response()->json(['msg' => 'Incorrect credentials'], 401);
		}

		try {
 			// Инициализация клиента для отправки запроса на получение JWT токена
			$client = new Client();
			$res = $client->post(env('APP_URL') . '/token', [
				'headers' => [
					'Authorization' => 'Bearer ' . $req->cookie('JWT_TOKEN'),
				],
				'json' => [
					'sub' => 'user',
					'login' => $data['login'],
				]
			]);

			// Декодирование ответа и проверка наличия токена
			$jwt = json_decode($res->getBody()->getContents());

			// Если токен отсутствует, возвращаем ошибку
			if (!isset($jwt->token)) {
				return response()->json($jwt);
			}

			// Возвращаем JWT токен и сохраняем его в cookie
			return response()->json($jwt)->withCookie(Cookie::make('JWT_TOKEN', $jwt->token, 3, '/', null, null, 1));

		} catch (\Exception $e) {
			
			// Обработка ошибок и возврат сообщения об ошибке
			return response()->json([
				'status' => 'error',
				'message' => $e->getMessage(),
			], 500);

		}

  }   
}
