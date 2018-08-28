<?php declare(strict_types=1);

// Define PATH for easier file includes
$app_path = substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR)) . '/app/';
define('PATH', $app_path);

// For security reasons, allow http-cookies only
ini_set('session.cookie_httponly', '1');
session_start();
