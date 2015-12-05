<?php
require __DIR__ . '/rb.php';
require __DIR__ . '/util.php';
require __DIR__ . '/models/user.php';

setupErrorHandling();

if (file_exists(__DIR__ . '/db.php')) {
	require __DIR__ . '/db.php';
} else {
	R::setup('mysql:host=localhost;dbname=skyhigh', 'root', 'admin');
}

session_start();