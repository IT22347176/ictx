<?php
$servername = "localhost";
$username = "root"; // Assuming you're using the default root user
$password = "";     // No password
$dbname = "ictx";   // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
