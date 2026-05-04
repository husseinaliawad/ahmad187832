<?php
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$dbName = getenv('DB_NAME') ?: 'city_events';
$dbPort = (int)(getenv('DB_PORT') ?: 3306);

/*
 * DB_HOST can contain one host or a comma-separated fallback list.
 * Example: "mysql,host.docker.internal,127.0.0.1"
 */
$configuredHosts = trim((string)(getenv('DB_HOST') ?: ''));
$hosts = $configuredHosts !== ''
    ? array_filter(array_map('trim', explode(',', $configuredHosts)))
    : ['mysql', 'host.docker.internal', '127.0.0.1', 'localhost'];

$conn = null;
$lastError = '';

mysqli_report(MYSQLI_REPORT_OFF);

foreach ($hosts as $host) {
    $try = @new mysqli($host, $dbUser, $dbPass, $dbName, $dbPort);
    if (!$try->connect_errno) {
        $conn = $try;
        break;
    }
    $lastError = $try->connect_error;
}

if (!$conn) {
    http_response_code(500);
    die(
        'فشل الاتصال بقاعدة البيانات. '
        . 'تحقق من DB_HOST/DB_PORT ووجود خدمة MySQL. '
        . 'آخر خطأ: ' . $lastError
    );
}

$conn->set_charset('utf8mb4');
?>
