<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM videos WHERE uploder=? ORDER BY id DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$videos = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Video Studio</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>

<div class="main-container">

<nav class="sidebar">
    <div class="logo">
        <a href="main.php">Loopz</a>
    </div>

<ul>
    <li class="section-title">Menu</li>
    <li><i class="fa fa-home"></i><a href="main.php">Home</a></li>
    <li><i class="fa fa-globe"></i><a href="explore.php">Explore</a></li>
    <li><i class="fa fa-upload"></i><a href="upload.php">Upload</a></li>

    <li class="section-title">Library</li>
    <li><i class="fa fa-heart"></i><a href="liked.php">Liked</a></li>
    <li><i class="fa fa-edit"></i><a href="video_studio.php">Video Studio</a></li>

    <li class="section-title">Account</li>
    <li><i class="fa fa-user"></i><a href="profile.php">Profile</a></li>
    <li><i class="fa fa-cog"></i><a href="setting.html">Settings</a></li>

    <li class="section-title">More</li>
    <li><i class="fa fa-question-circle"></i><a href="help.html">Help</a></li>
</ul>

    <!-- SIDEBAR FOOTER -->
    <div class="sidebar-footer">
        <p>© All Right Reserved <?php echo date('Y'); ?> Loopz</p>
        <a href="">About</a> ·
        <a href="">Privacy</a>
    </div>
</nav>

<div class="studio-container">
    <h2>🎬 Video Studio</h2>

    <div class="studio-grid">
        <div class="studio-card create-card" onclick="openEditor()">
            <i class="fa fa-plus"></i>
            <p>Create</p>
        </div>

        <?php while ($v = $videos->fetch_assoc()): ?>
    <div class="studio-card">

        <?php if ($v['media_type'] === 'video'): ?>
            <video
                src="videos/<?php echo htmlspecialchars($v['filename']); ?>"
                
                playsinline
            ></video>
        <?php else: ?>
            <img
                src="videos/<?php echo htmlspecialchars($v['filename']); ?>"
                alt="Media"
                class="studio-image"
            >
        <?php endif; ?>

    </div>
<?php endwhile; ?>

    </div>
</div>
</div>

<!-- EDITOR -->
<div class="editor-modal" id="editorModal">
    <div class="editor-box">

        <div class="editor-preview" id="editorPreview"></div>

        <div class="editor-tools">
            <input type="file" id="mediaInput" accept="video/*,image/*">
            <input type="file" name="music"id="musicInput" accept="audio/*">

            <input
                type="text"
                id="captionInput"
                placeholder="Write a caption..."
                maxlength="150"
            >

            <button onclick="applyFilter()">Apply Filter</button>

            <button class="upload-btn" id="uploadBtn">Upload</button> 
            <button class="close-btn" onclick="closeEditor()">Close</button>
            <div id="loading" class="loading">⏳ Processing…</div>

           

        </div>

    </div>
</div>

<script src="studio.js"></script>

</body>
</html>
