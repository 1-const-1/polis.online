<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Контроллер для обработки регистрации пользователя.
 * 
 * Этот класс предоставляет метод для регистрации нового пользователя, создания
 * пароля и получения JWT токена после успешной регистрации.
 */
class OnSignup extends Controller
{
		/**
	 * Регистрация нового пользователя и получение JWT токена.
	 *
	 * Проверяет, существует ли уже пользователь с таким логином. Если пользователь
	 * не найден, то создается новый пользователь, генерируется пароль и сохраняется
	 * в базе данных. Затем отправляется запрос для получения JWT токена, который
	 * возвращается с cookie.
	 *
	 * @param Request $req Объект запроса, содержащий данные для регистрации.
	 * @return \Illuminate\Http\JsonResponse Ответ с токеном или ошибкой.
	 */
	public function signup (Request $req) {
		 // Валидация входных данных
    $data = $req->validate([
			'login' => 'required|string',
			'pass' => 'required|string'
		]);

		 // Проверка, существует ли пользователь с таким логином
		$user = DB::table('users')->where('login', '=', $data['login'])->first();

		if ($user) {
			// Если пользователь существует, возвращаем ошибку
			return response()->json(['msg' => 'User exists'], 401);
		}

		 // Хеширование пароля
		$hashedPass = Hash::make($data['pass']);

		// Сохранение нового пользователя в базе данных
		DB::table("users")->insert([
			'login' => $data['login'],
			'password' => $hashedPass,
			'created_at' => now(),
			'updated_at' => now(),
		]);

		try {
			// Инициализация клиента для отправки запроса на получение JWT токена
			$client = new Client();
			$res = $client->post(env('APP_URL') . '/token', [
				'headers' => [
					'Authorization' => 'Bearer ' . $req->cookie('JWT_TOKEN'),
					'Content-Type' => 'application/json',
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
