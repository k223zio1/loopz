<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    exit("Unauthorized");
}

$username = $_SESSION['username'];
$video_id = intval($_POST['video_id'] ?? 0);

// Get video info
$stmt = $conn->prepare("SELECT filename FROM videos WHERE id=? AND uploder=?");
$stmt->bind_param("is", $video_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    // Delete video file
    $file = "videos/" . $row['filename'];
    if (file_exists($file)) {
        unlink($file);
    }

    // Delete database row
    $del = $conn->prepare("DELETE FROM videos WHERE id=?");
    $del->bind_param("i", $video_id);
    $del->execute();
}

header("Location: profile.php");
exit();
