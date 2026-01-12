<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";

$username = $_SESSION['username'];
$videos = [];

/* =========================
   GET LIKED VIDEOS
========================= */
$stmt = $conn->prepare("
    SELECT v.*,
    1 AS liked
    FROM videos v
    INNER JOIN video_likes l
        ON l.video_id = v.id
    WHERE l.username = ?
    ORDER BY v.id DESC
");

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $videos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Liked Videos | Loopz</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>

<div class="main-container">

<!-- SIDEBAR -->
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
    <li><i class="fa fa-cog"></i><a href="settings.php">Settings</a></li>

    <li class="section-title">More</li>
    <li><i class="fa fa-question-circle"></i><a href="help.php">Help</a></li>
</ul>

    <!-- SIDEBAR FOOTER -->
    <div class="sidebar-footer">
        <p>© All Right Reserved <?php echo date('Y'); ?> Loopz</p>
        <a href="about.php">About</a> ·
        <a href="privacy.php">Privacy</a>
    </div>
</nav>

<div class="top-profile" id="profileToggle">
    <div class="fa fa-user">
        <div class="profile-dropdown" id="profileDropdown">
            <p class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
            <a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
            <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>

<!-- FEED -->
<div class="liked-grid">

<?php if (empty($videos)): ?>
    <p class="empty-msg">❤️ No liked videos yet</p>
<?php endif; ?>

<?php foreach ($videos as $video): ?>
    <div class="grid-item">

        <video
            src="videos/<?php echo htmlspecialchars($video['filename']); ?>"
            muted
            playsinline
        ></video>

        <div class="grid-overlay">
            <i class="fa fa-heart"></i>
            <span><?php echo $video['likes']; ?></span>
        </div>

    </div>
<?php endforeach; ?>

</div>

</div>

<!-- ================= JS ================= -->
<script src="liked.js"></script>

</body>
</html>
    