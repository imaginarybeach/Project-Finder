<?php
$servername = "localhost";
$username = "root"; // Default username for MAMP
$password = "root"; // Default password for MAMP
$dbname = "my_website_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
