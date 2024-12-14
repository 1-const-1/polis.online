<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Класс для работы с JSON Web Tokens (JWT).
 * 
 * Этот класс предоставляет методы для создания, декодирования и проверки JWT,
 * а также для управления метаданными токена, такими как издатель (iss),
 * время выпуска (iat) и время истечения (exp).
 */
class JWT extends Controller
{
	/**
	 * @var string|null Хранит сгенерированный JWT.
	 */
	public ?string $token = null; 

	/**
	 * @var string|null Издатель JWT.
	 */
	public ?string $iss = null;

	/**
	 * @var int Время выпуска токена в формате Unix timestamp.
	 */
	public int $iat = 0;

	
	/**
	 * @var int Время истечения токена в формате Unix timestamp.
	 */
	public int $exp = 0;

	
	/**
	 * Генерирует JWT на основе данных payload и секретного ключа.
	 *
	 * @param array $payload Данные для полезной нагрузки JWT.
	 * @param string $alg Алгоритм хэширования для подписи. По умолчанию "sha256".
	 * @param string|null $secret Секретный ключ для подписания токена. Если не указан, используется пустая строка.
	 * @return void
	 */
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
	
	/**
	 * Устанавливает значения для свойств iss, iat и exp.
	 *
	 * @param string $iss Издатель JWT.
	 * @param int $iat Время выпуска токена в Unix timestamp.
	 * @param int $exp Время истечения в минутах от времени выпуска.
	 * @return void
	 */	
	public function issue (string $iss, int $iat, int $exp): void {
		$this->iss = $iss;
		$this->iat = $iat;
		$this->exp = $this->iat + (60 * $exp);
	}

	/**
	 * Извлекает и декодирует заголовок JWT.
	 *
	 * @param string $jwt Строка JWT.
	 * @return object Декодированный заголовок.
	 */
	static public function getHeader(string $jwt): object {
		preg_match('/^([^.]+)\./', $jwt, $m);
		return json_decode(self::base64UrlDecode($m[1]));
	}

	/**
	 * Извлекает и декодирует payload JWT.
	 *
	 * @param string $jwt Строка JWT.
	 * @return object Декодированный payload.
	 */
	static public function getPayload(string $jwt): object {
		preg_match('/\.([^.]+)\./', $jwt, $m);
		return json_decode(self::base64UrlDecode($m[1]));
	}

	/**
	 * Извлекает подпись JWT.
	 *
	 * @param string $jwt Строка JWT.
	 * @return string Подпись JWT.
	 */
	static public function getSignature(string $jwt): string {
		preg_match('/([^.]+)$/', $jwt, $m);
		return $m[1];
	}
	
	/**
	 * Кодирует строку в формат Base64 URL.
	 *
	 * @param string $data Данные для кодирования.
	 * @return string Закодированная строка.
	 */
	static private function base64UrlEncode (string $data): string {
		return str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($data));
	}

	/**
	 * Декодирует строку из формата Base64 URL.
	 *
	 * @param string $data Строка для декодирования.
	 * @return string Декодированная строка.
	 */
	static private function base64UrlDecode (string $data): string {
		return base64_decode(str_replace(["-", "_"], ["+", "/"], $data));
	}

	/**
	 * Проверяет, истек ли срок действия токена.
	 *
	 * @param string $jwt Строка JWT.
	 * @return bool Возвращает true, если токен истек, иначе false.
	 */	
	static public function isExpired (string $jwt) {
		$p = self::getPayload($jwt);
		return ($p->exp ?? 0) < time();
	}

}
