<?php
session_start();
require_once "config.php";

$username = $_SESSION['username'];
$id = intval($_POST['id']);
$comment = trim($_POST['comment']);

$stmt = $conn->prepare(
    "INSERT INTO comments (video_id, username, comment) VALUES (?, ?, ?)"
);
$stmt->bind_param("iss", $id, $username, $comment);
$stmt->execute();

echo json_encode([
    "username" => $username,
    "comment" => htmlspecialchars($comment)
]);
