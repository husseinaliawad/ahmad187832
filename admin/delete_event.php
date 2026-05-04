<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db.php';
$eventId = (int)$_GET['id'];

$fetch = $conn->prepare('SELECT image FROM events WHERE id = ?');
$fetch->bind_param('i', $eventId);
$fetch->execute();
$imageResult = $fetch->get_result();
$imagePath = '';
if ($imageResult->num_rows === 1) {
    $imageRow = $imageResult->fetch_assoc();
    $imagePath = $imageRow['image'];
}

$delete = $conn->prepare('DELETE FROM events WHERE id = ?');
$delete->bind_param('i', $eventId);
if ($delete->execute()) {
    if ($imagePath && str_starts_with($imagePath, 'assets/img/')) {
        $fullPath = realpath(__DIR__ . '/../' . $imagePath);
        $allowedRoot = realpath(__DIR__ . '/../assets/img');
        if ($fullPath && $allowedRoot && str_starts_with($fullPath, $allowedRoot) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}

header('Location: dashboard.php?status=deleted');
exit;
?>
