<?php
$servername = "localhost"; // Use localhost if running MySQL on the same machine
$port = 3306; // Default MySQL port
$username = "root";
$password = "root";
$dbname = "Project-Finder";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Your database queries here
$sql = "SELECT * FROM student-users";
$result = $conn->query($sql);

// Process the results
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Email: " . $row["email"]. "<br>";
    }
} else {
    echo "No results found";
}

$conn->close();
?>
