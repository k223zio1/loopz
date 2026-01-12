<?php
session_start();
header('Content-Type: application/json');
require_once "config.php";

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$username = $_SESSION['username'];

/* OPTIONAL: delete user videos first */
$stmt = $conn->prepare("DELETE FROM videos WHERE uploder = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

/* Delete user account */
$stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

/* Destroy session */
session_destroy();

echo json_encode(['success' => true]);
