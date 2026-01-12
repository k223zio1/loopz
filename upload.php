<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Video | Looply</title>

<!-- External CSS -->
 <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="upload_style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>

<body>

<div>
    <div class="social-icons-bg">
        <i class="fa fa-plus-square"></i>
        <i class="fa fa-upload"></i>
        <i class="fa fa-video-camera"></i>
        <i class="fa fa-camera"></i>
        <i class="fa fa-picture-o"></i>
        <i class="fa fa-edit"></i>
    </div>





    <div class="upload-container">
        <h2>Upload Video</h2>

        <form action="upload_process.php" method="POST" enctype="multipart/form-data">
            <label>Video Title</label>
            <input type="text" name="title" placeholder="Enter video title" required>

            <label>Select Video</label>
            <input type="file" name="video_file" accept="video/mp4,video/webm" required>

            <button type="submit">Upload</button>
        </form>

        <a href="main.php" class="back-link">← Back to Feed</a>
    </div>


</div>

<script src="js/main.js"></script>


</body>
</html>
