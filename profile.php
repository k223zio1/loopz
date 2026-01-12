<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";

$username = $_SESSION['username'];

/* User info */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* User videos */
$stmt = $conn->prepare("SELECT * FROM videos WHERE uploder=? ORDER BY id DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$videos = [];
$totalLikes = 0;
while ($row = $result->fetch_assoc()) {
    $videos[] = $row;
    $totalLikes += $row['likes'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($username); ?> | Profile</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>

<div class="main-container">

    <!-- SIDEBAR -->
<<nav class="sidebar">
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

    <!-- PROFILE PAGE -->
    <div class="profile-page">

        <!-- HEADER -->
        <div class="profile-header">

            <!-- AVATAR -->
            <div class="profile-avatar-wrapper">
                <img
                    src="avatars/<?php echo htmlspecialchars($user['profile_pic']); ?>"
                    class="profile-avatar-lg"
                >

                <!-- CHANGE PICTURE BUTTON (STACKED ON AVATAR) -->
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <label class="edit-avatar">
                        <input type="file" name="profile_pic" hidden onchange="this.form.submit()">
                        <i class="fa fa-camera"></i>
                    </label>
                </form>
            </div>

            <!-- SETTINGS ICON -->
<div class="profile-settings">
    <i class="fa fa-cog" id="settingsToggle"></i>

    <div class="settings-menu" id="settingsMenu">
        <a href="setting.html">
            <i class="fa fa-sliders"></i> General
        </a>

        <form action="logout.php" method="POST">
            <button type="submit">
                <i class="fa fa-sign-out"></i> Logout
            </button>
        </form>
    </div>
</div>


            <h2>@<?php echo htmlspecialchars($username); ?></h2>

            <div class="profile-stats">
                <div>
                    <strong><?php echo count($videos); ?></strong>
                    <span>Videos</span>
                </div>
                <div>
                    <strong><?php echo $totalLikes; ?></strong>
                    <span>Likes</span>
                </div>
            </div>
        </div>

        <!-- VIDEO GRID -->
        <div class="profile-video-grid">
            <?php if (empty($videos)): ?>
                <p class="empty-profile">No videos yet</p>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <div class="profile-video-card">

                        <video
                            src="videos/<?php echo $video['filename']; ?>"
                            muted
                            loop
                        ></video>

                        <!-- 🔒🌍 PRIVACY TOGGLE (THIS IS WHERE IT GOES) -->
                        <form method="POST" action="toggle_privacy.php" class="privacy-toggle">
                            <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                            <button type="submit" title="Toggle privacy">
                                <i class="fa <?php echo $video['is_private'] ? 'fa-lock' : 'fa-globe'; ?>"></i>
                            </button>
                        </form>

                        <!-- DELETE VIDEO BUTTON -->
                        <form method="POST" action="delete_video.php" class="delete-video">
                            <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                            <button type="submit" title="Delete video" onclick="return confirm('Delete this video?')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

                        <!-- LIKE OVERLAY -->
                        <div class="video-overlay">
                            <i class="fa fa-heart"></i> <?php echo $video['likes']; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>

<script src="function.js"></script>
</html>
