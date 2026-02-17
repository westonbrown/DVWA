<?php
require("vendor/autoload.php");

$openapi = \OpenApi\Generator::scan(['./src']);

function dvwa_api_set_cors_headers(): void {
	$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
	$allowlist = getenv('DVWA_API_CORS_ALLOW_ORIGINS');

	// Default deny unless explicitly allowlisted.
	if ($allowlist !== false && $allowlist !== '') {
		$allowed = array_filter(array_map('trim', explode(',', $allowlist)));
		if ($origin !== '' && in_array($origin, $allowed, true)) {
			header("Access-Control-Allow-Origin: {$origin}");
			header('Vary: Origin');
		}
	}

	header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

dvwa_api_set_cors_headers();

header("Content-Type: application/json; charset=UTF-8");
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();
