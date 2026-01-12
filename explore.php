<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";

$username = $_SESSION['username'];

$search = $_GET['q'] ?? '';
$filter = $_GET['filter'] ?? 'all';

if (!empty($search)) {
    $like = "%$search%";

    if ($filter === 'title') {

        $stmt = $conn->prepare("
            SELECT * FROM videos
            WHERE (is_private = 0 OR uploder = ?)
            AND title LIKE ?
            ORDER BY id DESC
        ");
        $stmt->bind_param("ss", $username, $like);

    } elseif ($filter === 'user') {

        $stmt = $conn->prepare("
            SELECT * FROM videos
            WHERE (is_private = 0 OR uploder = ?)
            AND uploder LIKE ?
            ORDER BY id DESC
        ");
        $stmt->bind_param("ss", $username, $like);

    } else {

        $stmt = $conn->prepare("
            SELECT * FROM videos
            WHERE (is_private = 0 OR uploder = ?)
            AND (title LIKE ? OR uploder LIKE ?)
            ORDER BY id DESC
        ");
        $stmt->bind_param("sss", $username, $like, $like);
    }

    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $stmt = $conn->prepare("
        SELECT * FROM videos
        WHERE is_private = 0 OR uploder = ?
        ORDER BY RAND()
        LIMIT 30
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Explore | Loopz</title>
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

    <!-- EXPLORE CONTENT (RIGHT SIDE) -->
    <div class="explore-content">

        <!-- SEARCH BAR -->
        <div class="explore-search">
            <form method="GET" action="explore.php">
    <input 
        type="text" 
        name="q" 
        placeholder="Search title or user…"
        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
    >

    <select name="filter">
        <option value="all" <?php if(($_GET['filter'] ?? '') === 'all') echo 'selected'; ?>>All</option>
        <option value="title" <?php if(($_GET['filter'] ?? '') === 'title') echo 'selected'; ?>>Title</option>
        <option value="user" <?php if(($_GET['filter'] ?? '') === 'user') echo 'selected'; ?>>User</option>
    </select>

    <button type="submit">
        <i class="fa fa-search"></i>
    </button>
</form>

        </div>

        <!-- VERTICAL FEED -->
        <div class="explore-feed">
            <?php while ($video = $result->fetch_assoc()): ?>
                <div class="explore-video-card">
                    <video
                        class="explore-video"
                        src="videos/<?php echo htmlspecialchars($video['filename']); ?>"
                        controls
                        loop
                    ></video>

                    <div class="explore-meta">
                        <span>@<?php echo htmlspecialchars($video['uploder']); ?></span>
                        <span><i class="fa fa-heart"></i> <?php echo $video['likes']; ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

</div>

</body>
</html>
