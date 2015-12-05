<?php
class ValidationException extends Exception {}

function pr($stuff) {
	echo __FILE__ . ":" . __LINE__ . "<br>\n";
	echo '<pre>', print_r($stuff, true), '</pre>';
}

function setupErrorHandling() {
	$errorHandler = function($e) {
		if ($e instanceof ValidationException) {
			header("HTTP/1.1 400 Bad Request");
		} else {
			header("HTTP/1.1 500 Internal Server Error");
		}

		$ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

		if ($ajax) {
			header("Content-type: application/json");
			echo json_encode([
				'code' => $e->getCode(),
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => $e->getTrace(),
			]);
		} else {
			print_r($e);
		}

		exit(1);
	};

	set_exception_handler($errorHandler);
	set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) use($errorHandler) {
		$errorHandler(new ErrorException($errstr, $errno));
	});
}

function h($string) {
	return htmlentities($string);
}

function me() {
	return !empty($_SESSION['user_id']) ? R::load('user', $_SESSION['user_id']) : null;
}

function signin($username, $password) {
	$user = R::findOne('user', 'username = ?', [$username]);

	if (!$user) {
		throw new ValidationException("Invalid username. Please try again.");
	}

	if (!password_verify($password, $user->password)) {
		throw new ValidationException("Wrong password. Please try again.");
	}

	$_SESSION['user_id'] = $user->id;
}

function signout() {
	session_unset();
	session_destroy();
	session_regenerate_id(true);
}

function redirect($url) {
	header("Location: $url");
	exit();
}

function restrict() {
	if (!me()) {
		header("HTTP/1.1 403 Forbidden");
		header("Location: index.php");
		exit();
	}
}

function postvar($name, $default = null) {
	return isset($_POST[$name]) ? $_POST[$name] : $default;
}

function getvar($name, $default = null) {
	return isset($_GET[$name]) ? $_GET[$name] : $default;
}