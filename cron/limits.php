<?php
session_start();
$_SESSION = array();
$_SESSION['user_status'] = 10; //'administrator';
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']); //повний шлях до цього файлу
chdir($path_parts['dirname']); //задаєм директорію цього файлу як поточну
chdir('..'); //задаєм директорію index.php як поточну для коректної обробки відносних шляхів на файли
require_once 'index.php';