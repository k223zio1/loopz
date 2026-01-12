<?php
$conn = new mysqli("localhost", "USERNAME", "PASSWORD", "DATABASE");

if ($conn->connect_error) {
    die("Database connection failed");
}
