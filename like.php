<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$username = $_SESSION['username'];
$video_id = (int)$_GET['id'];

/* Check if already liked */
$stmt = $conn->prepare("
    SELECT id FROM video_likes 
    WHERE video_id = ? AND username = ?
");
$stmt->bind_param("is", $video_id, $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // 🔴 UNLIKE
    $stmt = $conn->prepare("
        DELETE FROM video_likes 
        WHERE video_id = ? AND username = ?
    ");
    $stmt->bind_param("is", $video_id, $username);
    $stmt->execute();

    $conn->query("
        UPDATE videos 
        SET likes = GREATEST(likes - 1, 0) 
        WHERE id = $video_id
    ");

    $liked = false;
} else {
    // ❤️ LIKE
    $stmt = $conn->prepare("
        INSERT INTO video_likes (video_id, username)
        VALUES (?, ?)
    ");
    $stmt->bind_param("is", $video_id, $username);
    $stmt->execute();

    $conn->query("
        UPDATE videos 
        SET likes = likes + 1 
        WHERE id = $video_id
    ");

    $liked = true;
}

/* Return updated count */
$res = $conn->query("SELECT likes FROM videos WHERE id = $video_id");
$row = $res->fetch_assoc();

echo json_encode([
    'likes' => $row['likes'],
    'liked' => $liked
]);
