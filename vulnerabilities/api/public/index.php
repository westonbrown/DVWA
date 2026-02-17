<?php

require '../bootstrap.php';
use Src\UserController;
use Src\HealthController;
use Src\GenericController;
use Src\OrderController;
use Src\LoginController;
use Src\Helpers;

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

	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

// CORS + common headers

dvwa_api_set_cors_headers();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

# As DVWA can be installed in any directory, this works out where
# the API is so we know the base to start from.

$local_uri = array();
foreach ($uri as $pos => $dir) {
	if ($dir == "order" || $dir == "user" || $dir == "health" || $dir == "login") {
		$local_uri = array_slice ($uri, $pos - 1);
		break;
	}
}

// All of our endpoints start with /api/v[0-9]
// everything else results in a 404 Not Found

if (count($local_uri) < 2) {
	header("HTTP/1.1 404 Not Found");
	exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$version = $local_uri[0];

if (preg_match ("/v([0-9]*)/", $version, $matches)) {
	$version = intval ($matches[1]);
} else {
	header("HTTP/1.1 404 Not Found");
	exit();
}
$controller = $local_uri[1];

switch ($controller) {
	case "order":
		// the user id is, of course, optional and must be a number:
		$orderId = null;
		if (isset($local_uri[2]) && $local_uri[2] != "") {
			$orderId = intval($local_uri[2]);
		}

		// pass the request method and order ID to the OrderController and process the HTTP request:
		$controller = new OrderController($requestMethod, $version, $orderId);
		$controller->processRequest();
		break;
	case "user":
		// the user id is, of course, optional and must be a number:
		$userId = null;
		if (isset($local_uri[2])) {
			$userId = (int) $local_uri[2];
		}

		// pass the request method and user ID to the UserController and process the HTTP request:
		$controller = new UserController($requestMethod, $version, $userId);
		$controller->processRequest();
		break;
	case "health":
		if (!isset($local_uri[2])) {
			$gc = new GenericController("notFound");
			$gc->processRequest();
			break;
		}

		$command = $local_uri[2];
		$controller = new HealthController($requestMethod, $version, $command);
		$controller->processRequest();
		break;
	case "login":
		if (!isset($local_uri[2])) {
			$gc = new GenericController("notFound");
			$gc->processRequest();
			break;
		}

		$command = $local_uri[2];
		$controller = new LoginController($requestMethod, $version, $command);
		$controller->processRequest();
		break;
	default:
		$gc = new GenericController("notFound");
		$gc->processRequest();
		break;
}
