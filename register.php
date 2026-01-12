<?php
session_start();
require_once "config.php";

if ($conn->connect_error) {
	die("Connection failed");
}

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

if ($conn->query($sql) === TRUE) {
	// Redirect to login page after success
	header("Location: index.html");
	exit();
} else {
	echo "Error: Username already exists";
}

$conn->close();
?>
