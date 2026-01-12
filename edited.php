<?php
ob_start();
session_start();
header('Content-Type: application/json');
require_once "config.php";

if (!isset($_SESSION['username'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$uploader = $_SESSION['username'];
$uploadDir = "videos/";
$musicDir  = "uploads/";

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($musicDir)) mkdir($musicDir, 0777, true);

if (!isset($_FILES['media'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'No media uploaded']);
    exit;
}

$media = $_FILES['media'];
$ext = strtolower(pathinfo($media['name'], PATHINFO_EXTENSION));

$allowed = ['jpg','jpeg','png','mp4','mov','webm'];
if (!in_array($ext, $allowed)) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit;
}

$baseName = time() . "_" . uniqid();
$outputVideo = $uploadDir . $baseName . ".mp4";

$ffmpeg = "C:\\ffmpeg\\bin\\ffmpeg.exe";

/* =========================
   IMAGE → VIDEO + MUSIC
========================= */
if (in_array($ext, ['jpg','jpeg','png'])) {

    if (!isset($_FILES['music'])) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Music required']);
        exit;
    }

    $imagePath = $uploadDir . $baseName . "." . $ext;
    if (!move_uploaded_file($media['tmp_name'], $imagePath)) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Image upload failed']);
        exit;
    }

    $music = $_FILES['music'];
    $musicPath = $musicDir . time() . "_" . basename($music['name']);
    if (!move_uploaded_file($music['tmp_name'], $musicPath)) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Music upload failed']);
        exit;
    }

    $cmd = "\"$ffmpeg\" -y "
         . "-loop 1 -framerate 30 -i \"$imagePath\" "
         . "-i \"$musicPath\" "
         . "-c:v libx264 "
         . "-vf \"scale=trunc(iw/2)*2:trunc(ih/2)*2\" "
         . "-pix_fmt yuv420p "
         . "-map 0:v:0 -map 1:a:0 "
         . "-c:a aac -b:a 192k "
         . "-shortest "
         . "\"$outputVideo\" 2>&1";

    exec($cmd, $out, $ret);

    if ($ret !== 0) {
        ob_clean();
        echo json_encode([
            'success' => false,
            'error' => 'FFmpeg failed',
            'debug' => $out
        ]);
        exit;
    }
}

/* =========================
   VIDEO UPLOAD
========================= */
else {
    if (!move_uploaded_file($media['tmp_name'], $outputVideo)) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Video upload failed']);
        exit;
    }
}

/* =========================
   SAVE DATABASE
========================= */
$title = $_POST['caption'] ?? 'Edited Media';

$stmt = $conn->prepare(
    "INSERT INTO videos (title, filename, uploder, likes, comments)
     VALUES (?, ?, ?, 0, '')"
);
$stmt->bind_param("sss", $title, basename($outputVideo), $uploader);
$stmt->execute();

ob_clean();
echo json_encode(['success' => true]);
exit;
