<?php
session_start();
include 'navbar.php';
// Start the session

// Check if user is logged in
if (isset($_SESSION['NetID'])) {
    $netID = $_SESSION['NetID'];

    // Database connection (replace with your actual database credentials)
    $servername = "db";
    $username = "root";
    $password = "rooty";
    $dbname = "Project-Finder";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch user data based on NetID
    $stmt = $conn->prepare("SELECT NetID, Email, Phone, Name, Pronouns FROM STUDENT WHERE NetID = ?");
    $stmt->bind_param("s", $netID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
    } else {
        // Handle case where no user is found with that NetID
        echo "No user found with NetID: " . htmlspecialchars($netID);
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to login page if NetID is not set in session
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .dashboard {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
            font-size: 20px;
        }
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .profile-info h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .profile-info p {
            margin: 5px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['Name']); ?></h1>
            <p><strong>NetID:</strong> <?php echo htmlspecialchars($user['NetID']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['Phone'] ?? 'Not provided'); ?></p>
            <p><strong>Pronouns:</strong> <?php echo htmlspecialchars($user['Pronouns'] ?? 'Not provided'); ?></p>
        </div>
    </div>
</body>
</html>