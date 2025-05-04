<?php

session_start();

$error = '';
$success = '';

$host = 'db'; // connection to docker
$dbname = 'Project_Finder'; // db name
$dbuser = 'root'; // db admin
$dbpass = 'rooty'; // db password

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// log in user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['NetID']); // 
    $currentPassword = trim($_POST['CurrentPass']); //
    $newPassword = trim($_POST['NewPass']); //
    $confirmPassword = trim($_POST['ConfirmPass']); //

    if (!empty($username) && !empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
        // check if new passwords match
        if ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match.";
        } else {

            //unsecure sql code from the slides
            $conn = new mysqli($host, $dbuser, $dbpass, $dbname);
            $checkSql = "SELECT NetID FROM STUDENT WHERE NetID = '$username' AND Pass = '$currentPassword'";
            $checkResult = $conn->query($checkSql);
            
            if ($checkResult->num_rows === 1) {

                $updateSql = "UPDATE STUDENT SET Pass = '$newPassword' WHERE NetID = '$username'";
                
                if ($conn->query($updateSql) === TRUE) {
                    $success = "Password changed successfully!";
                } else {
                    $error = "Error updating password: " . $conn->error;
                }
            } else {
                $error = "Invalid NetID or current password.";
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>

        /* css code from online resources customized for our project 
        https://www.w3schools.com/css/
        */
        body {
            font-family: Arial, sans-serif;
            background-color: #585A7C;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .password-container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        .password-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .password-container form {
            display: flex;
            flex-direction: column;
        }

        .password-container label {
            margin-bottom: 5px;
            text-align: left;
            font-size: 0.9rem;
            color: #555;
        }

        .password-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .password-container button {
            padding: 10px;
            background-color: #9092C8;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }

        .password-container button:hover {
            background-color: #8082B4;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .success-message {
            color: green;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<!-- code snippets from online resources- 
 https://www.w3schools.com/tags/tag_label.asp 
 https://www.w3schools.com/php/php_echo_print.asp
 -->
<body>
    <div class="password-container">
        <h2>Change Password</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="POST" action="injection.php">
            <label for="NetID">Username:</label>
            <input type="text" id="NetID" name="NetID" required>
            <label for="CurrentPass">Current Password:</label>
            <input type="text" id="CurrentPass" name="CurrentPass" required>
            <label for="NewPass">New Password:</label>
            <input type="text" id="NewPass" name="NewPass" required>
            <label for="ConfirmPass">Confirm New Password:</label>
            <input type="text" id="ConfirmPass" name="ConfirmPass" required>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>