<?php
session_start();
$error = '';

$host = 'db'; // docker
$dbname = 'Project_Finder'; //name of db
$dbuser = 'root'; // admin user
$dbpass = 'rooty'; //admin pass

// Create connection
try {
    // mysqli code from the slides
    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    // error handling for debugging https://www.w3schools.com/php/func_mysqli_connect_error.asp
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['NetID']);
        $password = trim($_POST['Pass']);
        
        if (!empty($username) && !empty($password)) {
            // prevents SQL injection
            $sql = "SELECT NetID, Pass FROM STUDENT WHERE NetID = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if ($user['Pass'] === $password) {
                        $_SESSION['NetID'] = $user['NetID'];
                        header("Location: myprofile.php");
                        exit;
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
                $stmt->close();
            } else {
                throw new Exception("Database query error: " . $conn->error);
            }
        } else {
            $error = "Please fill in all fields.";
        }
    }

    //https://www.php.net/manual/en/function.error-log.php
    // error handling for debugging
} catch (Exception $e) {
    error_log($e->getMessage());
    $error = "A system error occurred. Please try again later.";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
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
        <!-- code snippets from online resources- 
 https://www.w3schools.com/tags/tag_label.asp 
 https://www.w3schools.com/php/php_echo_print.asp
 -->
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="NetID">Username:</label>
            <input type="text" id="NetID" name="NetID" required>
            <label for="Pass">Password:</label>
            <input type="password" id="Pass" name="Pass" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>