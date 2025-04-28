<?php
// Start session
session_start();

// Initialize variables
$error = '';
// Database connection
$host = 'db'; // Use 'localhost' if the database is hosted locally
$dbname = 'Project-Finder'; // Replace with your database name
$dbuser = 'root'; // Root user of the database
$dbpass = 'rooty'; // Replace with the root user's password (leave empty if no password is set)

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['Email']); // Use email as the username
    $password = trim($_POST['NetID']); // Use NetID as the password

    if (!empty($username) && !empty($password)) {
        // Query to check user credentials
        $stmt = $conn->prepare("SELECT NetID, Email FROM mytable WHERE Email = ? AND NetID = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['NetID'] = $user['NetID'];
            $_SESSION['Email'] = $user['Email'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or NetID.";
        }

        $stmt->close();
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
    <title>Login</title>
    <style>
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

        .login-container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        .login-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            margin-bottom: 5px;
            text-align: left;
            font-size: 0.9rem;
            color: #555;
        }

        .login-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .login-container button {
            padding: 10px;
            background-color: #9092C8;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #8082B4;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Log In</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="Email">Username:</label>
            <input type="text" id="Email" name="Email" required>
            <label for="NetID">Password:</label>
            <input type="password" id="NetID" name="NetID" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
