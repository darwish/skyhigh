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

function me() {
	return !empty($_SESSION['user_id']) ? R::load('user', $_SESSION['user_id']) : null;
}

function restrict() {
	if (!me()) {
		header("HTTP/1.1 403 Forbidden");
		header("Location: index.php");
		exit();
	}
}