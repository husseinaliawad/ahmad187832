<?php
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$dbName = getenv('DB_NAME') ?: 'city_events';
$dbPort = (int)(getenv('DB_PORT') ?: 3306);

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

if ($conn->connect_error) {
    die('فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
?>
