<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) exit;

$file = $_FILES['profile_pic'];

$allowed = ['jpg','jpeg','png','webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    header("Location: profile.php");
    exit;
}

$filename = uniqid() . "." . $ext;
$path = "avatars/" . $filename;

move_uploaded_file($file['tmp_name'], $path);

$stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE username=?");
$stmt->bind_param("ss", $filename, $_SESSION['username']);
$stmt->execute();

header("Location: profile.php");
