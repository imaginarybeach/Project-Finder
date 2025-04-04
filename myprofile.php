<?php
include 'navbar.php';
// Start the session
session_start();

// Dummy user data (replace with database data in a real application)
$user = [
    'username' => 'katy123',
    'email' => 'katy@example.com',
    'full_name' => 'Katy Soddy',
    'bio' => 'Description about Katy Soddy.',
];
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
            <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p>@<?php echo htmlspecialchars($user['username']); ?></p>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <p><?php echo htmlspecialchars($user['bio']); ?></p>
        </div>
    </div>
</body>
</html>