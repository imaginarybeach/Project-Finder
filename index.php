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
            padding: 1rem 0;
            text-align: center;
        }
        main {
            padding: 2rem;
            text-align: center;
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
        <h1>Welcome to Project Finder</h1>
    </header>
    <main>
        <p>Find and manage your projects with ease.</p>
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="login.php" style="text-decoration: none;">
            <button style="background-color: #9092C8; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold;">Log In</button>
            </a>
            <a href="register.php" style="text-decoration: none;">
            <button style="background-color: #8082B4; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold;">Register</button>
            </a>
            <a href="dashboard.php" style="text-decoration: none;">
            <button style="background-color: #6D6F9A; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold;">Browse Projects</button>
            </a>
            <a href="projects.php" style="text-decoration: none;">
            <button style="background-color: #585A7C; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold;">My Profile</button>
            </a>
        </div>
    </main>
    <footer>
        <p><?php echo date("Y"); ?> Project Finder. Katy Soddy and others.</p>
    </footer>
</body>
</html>