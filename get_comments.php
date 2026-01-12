<?php
require_once "config.php";
$id = intval($_GET['id']);

$stmt = $conn->prepare(
    "SELECT username, comment FROM comments WHERE video_id=? ORDER BY id DESC"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode($comments);
