<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";

$username = $_SESSION['username'];
$videos = [];

$stmt = $conn->prepare("
    SELECT v.*,
    EXISTS (
        SELECT 1 FROM video_likes l
        WHERE l.video_id = v.id
        AND l.username = ?
    ) AS liked
    FROM videos v
    WHERE v.is_private = 0
       OR v.uploder = ?
    ORDER BY v.id DESC
");

if (!$stmt) {
    die("SQL ERROR: " . $conn->error);
}

$stmt->bind_param("ss", $username, $username);
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
<title>Loopz Feed</title>
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

<div class="top-profile" id="profileToggle">
    <div class="fa fa-user">
        <div class="profile-dropdown" id="profileDropdown">
            <p class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
            <a href="setting.html"><i class="fa fa-cog"></i> Settings</a>
            <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>

<div class="feed">
<?php foreach($videos as $video): ?>
<div class="video-card">
    <div class="video-frame">

<?php if ($video['media_type'] === 'video'): ?>
    <video
        class="feed-video"
        src="videos/<?php echo htmlspecialchars($video['filename']); ?>"
        autoplay
        loop
        playsinline
    ></video>
<?php else: ?>
    <img
        class="feed-image"
        src="videos/<?php echo htmlspecialchars($video['filename']); ?>"
        alt="Post"
    >
<?php endif; ?>

<div class="caption">
    <h4><?php echo htmlspecialchars($video['uploder']); ?></h4>
    <p><?php echo htmlspecialchars($video['title']); ?></p>
</div>

</div>

    <div class="actions">
        <i class="fa fa-heart like-btn <?php echo $video['liked'] ? 'liked' : ''; ?>"
            data-id="<?php echo $video['id']; ?>"></i>
            <span class="like-count"><?php echo $video['likes']; ?></span><br>
            <button class="action-btn comment-btn" data-id="<?php echo $video['id']; ?>">
                <i class="fa fa-comment"></i>
            </button>
    </div>

    <!-- COMMENT MODAL -->
    <div id="comment-modal-<?php echo $video['id']; ?>" class="comment-modal" style="display:none;">
        <div class="modal-content">
            <span class="close-modal" data-id="<?php echo $video['id']; ?>">&times;</span>
            <h3>Comments</h3>
            <div id="comments-list-<?php echo $video['id']; ?>"></div>
            <textarea id="new-comment-<?php echo $video['id']; ?>" placeholder="Add a comment"></textarea>
            <button class="submit-comment" data-id="<?php echo $video['id']; ?>">Post</button>
        </div>
    </div>

    <div class="side-nav">
        <button class="side-btn up-btn">
            <i class="fa fa-chevron-up"></i>
        </button>

        <button class="side-btn down-btn">
            <i class="fa fa-chevron-down"></i>
        </button>
    </div>


</div>
<?php endforeach; ?>
</div>
</div>


<script src="function.js"></script>

</body>
</html>
