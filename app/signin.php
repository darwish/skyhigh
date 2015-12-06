<?php
require __DIR__ . '/../includes/start.php';
signin(postvar("username"), postvar("password"));

if (postvar("pay")) {
	$_SESSION['pay'] = 1;
}
?>