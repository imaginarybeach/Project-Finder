<?php
// Database connection settings
$host = 'db'; // Docker service name for MySQL
$dbname = 'Project_Finder';
$dbuser = 'root';
$dbpass = 'rooty';

// Create connection
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = $success = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $netid = isset($_POST['NetID']) ? trim($_POST['NetID']) : '';
    $password = isset($_POST['Pass']) ? trim($_POST['Pass']) : '';
    $verify_password = isset($_POST['VerifyPass']) ? trim($_POST['VerifyPass']) : '';
    $email = isset($_POST['Email']) ? trim($_POST['Email']) : '';
    $phone = isset($_POST['Phone']) ? trim($_POST['Phone']) : '';
    $name = isset($_POST['Name']) ? trim($_POST['Name']) : '';
    $pronouns = isset($_POST['Pronouns']) ? trim($_POST['Pronouns']) : '';

    // Check if all required fields are filled
    if (empty($netid) || empty($password) || empty($verify_password) || empty($email) || empty($name)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $verify_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if NetID or Email already exists
        $stmt = $conn->prepare("SELECT NetID, Email FROM STUDENT WHERE NetID = ? OR Email = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        $stmt->bind_param("ss", $netid, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close(); // Close the first statement

            // Insert the new user
            $stmt = $conn->prepare("INSERT INTO STUDENT (NetID, Pass, Email, Phone, Name, Pronouns) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }

            $stmt->bind_param("ssssss", $netid, $password, $email, $phone, $name, $pronouns);
            if ($stmt->execute()) {
                $success = "Account created successfully. You can now log in.";
            } else {
                $error = "Error creating account. Please try again.";
            }
            $stmt->close(); // Close the second statement
        } else {
            $error = "NetID or email already exists.";
            $stmt->close(); // Close the first statement
        }
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Project Finder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #585A7C;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .register-container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }

        .register-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
        }

        .register-container label {
            margin-bottom: 5px;
            text-align: left;
            font-size: 0.9rem;
            color: #555;
        }

        .register-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .register-container button {
            padding: 10px;
            background-color: #9092C8;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }

        .register-container button:hover {
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

        .required-field::after {
            content: " *";
            color: red;
        }

        .optional-field {
            color: #888;
        }

        .login-link {
            margin-top: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Project Finder Registration</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label for="netid">NetID:</label>
            <input type="text" id="netid" name="NetID" minlength="9" maxlength="9" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="Pass" minlength="10" maxlength="50" required>
            
            <label for="verify_password">Verify Password:</label>
            <input type="password" id="verify_password" name="VerifyPass" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="Email" required>
            
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="Phone">
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="Name" required>
            
            <label for="pronouns">Pronouns:</label>
            <input type="text" id="pronouns" name="Pronouns">
            
            <button type="submit">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>