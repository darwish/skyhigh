<?php
require __DIR__ . '/password.php';
require __DIR__ . '/rb.php';
require __DIR__ . '/util.php';
require __DIR__ . '/models/user.php';
require_once __DIR__ . '/generate-db.php';
	
setupErrorHandling();

if (file_exists(__DIR__ . '/db.php')) {
	require __DIR__ . '/db.php';
} else {
	R::setup('mysql:host=localhost;dbname=skyhigh', 'root', 'admin');
}

if (!R::findOne('item')) {
 	generateDB();
}
session_start();