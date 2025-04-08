<?php
?>
<link rel="stylesheet" href="/css/style.css">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Finder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        header {
            background: #9EA0DC;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        header h1 {
            font-size: 3rem;
            margin: 0;
        }
        main {
            padding: 2rem;
            text-align: center;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 2rem;
        }
        .button-container a {
            text-decoration: none;
        }
        .button-container button {
            background-color: #9092C8;
            color: white;
            border: none;
            padding: 15px 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .button-container button:hover {
            background-color: #8082B4;
        }
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Project Finder</h1>
    </header>
    <main>
        <div class="button-container">
            <a href="login.php">
                <button>Log In</button>
            </a>
            <a href="register.php">
                <button>Register</button>
            </a>
        </div>
    </main>
    <footer>
        <p><?php echo date("Y"); ?> Project Finder. Katy Soddy and others.</p>
    </footer>
</body>
</html>