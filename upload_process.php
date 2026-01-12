<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";

$uploader = $_SESSION['username'];
$title = $_POST['title'];

// Handle file upload
if(isset($_FILES['video_file'])){
    $file = $_FILES['video_file'];
    $filename = time() . "_" . basename($file['name']); // unique name
    $target = "videos/" . $filename;

    // Move uploaded file to folder
    if(move_uploaded_file($file['tmp_name'], $target)){
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO videos (title, filename, uploder, likes, comments) VALUES (?, ?, ?, 0, '')");
        $stmt->bind_param("sss", $title, $filename, $uploader);

        if($stmt->execute()){
            header("Location: main.php"); // redirect to feed
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "No file selected.";
}
?>
