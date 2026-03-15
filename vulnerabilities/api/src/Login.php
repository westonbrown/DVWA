<?php

namespace Src;

use OpenApi\Attributes as OAT;

class Login
{
	private const ACCESS_TOKEN_LIFE = 180;
	private const REFRESH_TOKEN_LIFE = 240;

	private static function getAccessTokenSecret(): string {
		$secret = getenv('DVWA_ACCESS_TOKEN_SECRET') ?: '';
		if (strlen($secret) < 32) {
			throw new \RuntimeException('DVWA_ACCESS_TOKEN_SECRET must be set to a 32+ character value');
		}
		return $secret;
	}

	private static function getRefreshTokenSecret(): string {
		$secret = getenv('DVWA_REFRESH_TOKEN_SECRET') ?: '';
		if (strlen($secret) < 32) {
			throw new \RuntimeException('DVWA_REFRESH_TOKEN_SECRET must be set to a 32+ character value');
		}
		return $secret;
	}
	
	public static function create_token() {
		$now = time();
		$tokenObj = new Token();
		$token = json_encode (array (
			"access_token" => $tokenObj->create_token(self::getAccessTokenSecret(), $now + self::ACCESS_TOKEN_LIFE),
			"refresh_token" => $tokenObj->create_token(self::getRefreshTokenSecret(), $now + self::REFRESH_TOKEN_LIFE),
			"token_type" => "bearer",
			"expires_in" => self::ACCESS_TOKEN_LIFE)
		);
		return $token;
	}

	public static function check_access_token($token) {
		$tokenObj = new Token();
		$decrypted = $tokenObj->decrypt_token ($token);

		if ($decrypted === false) {
			return false;
		}
		if ($decrypted['secret'] == self::getAccessTokenSecret() && $decrypted['expires'] > time()) {
			return true;
		}
		return false;
	}

	public static function check_refresh_token($token) {
		$tokenObj = new Token();
		$decrypted = $tokenObj->decrypt_token ($token);

		if ($decrypted['secret'] == self::getRefreshTokenSecret() && $decrypted['expires'] > time()) {
			return true;
		}
		return false;
	}
}
