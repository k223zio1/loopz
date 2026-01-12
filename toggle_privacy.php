<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    exit("Unauthorized");
}

$videoId = intval($_POST['video_id']);
$username = $_SESSION['username'];

$stmt = $conn->prepare("
    UPDATE videos 
    SET is_private = NOT is_private 
    WHERE id = ? AND uploder = ?
");
$stmt->bind_param("is", $videoId, $username);
$stmt->execute();

header("Location: profile.php");
exit;
