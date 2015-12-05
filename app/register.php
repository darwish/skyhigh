<?php
require __DIR__ . '/../includes/start.php';

$user = R::dispense('user');

$user->username = postvar("username");
$user->password = password_hash(postvar("password"), PASSWORD_BCRYPT);

R::store($user);

$_SESSION['user_id'] = $user->id;
?>