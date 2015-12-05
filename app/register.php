<?php
require __DIR__ . '/../includes/start.php';

$user = R::dispense('user');

$user->username = postvar("username");
$user->password = postvar("password");

R::store($user);

$_SESSION['user_id'] = $user->id;
?>