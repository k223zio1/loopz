<?php
session_start();
require_once "config.php";
if (!isset($_POST['g-recaptcha-response'])) {
    die('Please complete the reCAPTCHA');
}

$recaptchaSecret = "6LeE_UYsAAAAABqYyVlgABl_aSJ-WTkVfdyJAtfO";
$response = $_POST['g-recaptcha-response'];
$userIP = $_SERVER['REMOTE_ADDR'];

$verify = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify" .
    "?secret=$recaptchaSecret&response=$response&remoteip=$userIP"
);

$captchaSuccess = json_decode($verify);

if (!$captchaSuccess->success) {
    die('reCAPTCHA verification failed');
}

$username = $_POST['username'];
$password = $_POST['pass'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
	$user = $result->fetch_assoc();
	if (password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username'];

    echo "<script>
        alert('Login successful 🎉');
        window.location.href = 'main.php';
    </script>";
    exit();
	} else {
    echo "<script>
        alert('Wrong password ❌');
        window.history.back();
    </script>";
    exit();
	}
} else {
    echo "<script>
        alert('User not found ❌');
        window.history.back();
    </script>";
    exit();
}

?>
