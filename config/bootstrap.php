<?php declare(strict_types=1);

// Definiert dateipfad für einfachere Datei-Includes
$app_path = substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR)) . '/app/';
define('PATH', $app_path);

//Aus Sicherheitsgründen, sind nur  http-Cookies erlaubt!!
ini_set('session.cookie_httponly', '1');
session_start();
