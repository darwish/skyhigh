<?php
require __DIR__ . '/rb.php';
require __DIR__ . '/util.php';

setupErrorHandling();

R::setup('mysql:host=localhost;dbname=skyhigh', 'root', 'admin');
session_start();