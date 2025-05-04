<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['NetID'])) {
    $netID = $_SESSION['NetID'];

    // Database connection (replace with your actual database credentials)
    $servername = "db";
    $username = "root";
    $password = "rooty";
    $dbname = "Project_Finder";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET["project_id"])) {
        $stmt = $conn->prepare("SELECT * FROM PROJECT WHERE PID = ?");
        $stmt->bind_param("s", $_GET["project_id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $project = $result->fetch_assoc();
        if ($project["NetID"] != $netID) {
            $error = "You don't own this project!";
        }
    } else {
        header('Location: myprofile.php');
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["project_id"]) && isset($_POST["delete_project"])) {
        if ($project["NetID"] != $netID) {
            $error = "You don't own this project!";
        } else {
            $PID = $_POST["project_id"];
            
            $delStmt = $conn->prepare("DELETE FROM PROJECT
                                        WHERE PID = ? and NetID = ?;");
            $delStmt->bind_param("is",$PID, $netID);
            $delStmt->execute();
            if ($delStmt->affected_rows > 0) {
                $delStmt = $conn->prepare("DELETE FROM PROJECT_CODING_LANGUAGES
                                        WHERE PID = ?;");
                $delStmt->bind_param("i",$PID);
                $delStmt->execute();

                $delStmt = $conn->prepare("DELETE FROM WORKS_ON
                                        WHERE PID = ?;");
                $delStmt->bind_param("i",$PID);
                $delStmt->execute();

                header("Location: /myprofile.php");
            } else {
                $error = "Error deleting project.";
            }
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["project_id"]) && isset($_POST["edit_project"])) {
        if ($project["NetID"] != $netID) {
            $error = "You don't own this project!";
        } else {
            // Prepare and sanitize form data
            $projectName = $conn->real_escape_string($_POST['project_name']);
            $projectDescription = $conn->real_escape_string($_POST['project_description']);
            $projectTag = $conn->real_escape_string($_POST['project_tag']);
            
            $PID = $_POST["project_id"];
            
            // update project
            $updateStmt = $conn->prepare("UPDATE PROJECT
                                        SET Name = ?, Description = ?, Tag = ?
                                        WHERE PID = ? and NetID = ?;");
                                        
            $updateStmt->bind_param("sssis",  $projectName, $projectDescription, $projectTag, $PID, $netID);
            $updateStmt->execute();

            $delStmt = $conn->prepare("DELETE FROM PROJECT_CODING_LANGUAGES
            WHERE PID = ?;");
            $delStmt->bind_param("i",$PID);
            $delStmt->execute();

            $coding_languages = explode(",", $_POST['coding_languages']);
            $expLevels = explode(",", $_POST['experience_levels']);
                
            // Prepare statement for coding languages
            $langStmt = $conn->prepare("INSERT INTO PROJECT_CODING_LANGUAGES (PID, Language, Relative_experience) 
                                        VALUES (?, ?, ?)");
                
            for ($i = 0; $i < count($coding_languages); $i++) {
                $language = trim($conn->real_escape_string($coding_languages[$i]));
                $expLevel = isset($expLevels[$i]) ? trim($conn->real_escape_string($expLevels[$i])) : "Beginner";
                    
                if (!empty($language)) {
                        $langStmt->bind_param("iss", $PID, $language, $expLevel);
                        $langStmt->execute();
                }
            }
            $langStmt->close();
                
            $success = "Project edited successfully!";
        }
    }

    // execute select again to get upated proejct
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();

    $stmt = $conn->prepare("SELECT * FROM PROJECT_CODING_LANGUAGES WHERE PID = ?");
    $stmt->bind_param("s", $_GET["project_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $languages = "";
    $experiences = "";
    while ($row = $result->fetch_assoc()) {
        $languages = $languages . $row["Language"] . ", ";
        $experiences = $experiences . $row["Relative_experience"] . ", ";
    }
    $languages = substr($languages, 0, -2);
    $experiences = substr($experiences, 0, -2);


} else {
    // Redirect to login page if NetID is not set in session
    header('Location: login.php');
    exit();
}
include 'navbar.php';
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
        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .edit-project {
            text-align: left;
        }
        .edit-project h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .language-container {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .add-language-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        /* New styles for the projects section */
        .my-projects {
            margin-top: 30px;
            text-align: left;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        @media (min-width: 768px) {
            .edit-container {
                max-width: 800px;
            }
            .projects-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
<div class="edit-container">

<div class="edit-project">
<h2>Edit Project</h2>

<?php if(isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" >
    <input type="hidden" name="project_id" value="<?= $project['PID'] ?>">
    <div class="form-group">
        <label for="project_name">Project Name*:</label>
        <input type="text" id="project_name" name="project_name" required value="<?= $project["Name"] ?>">
    </div>
    
    <div class="form-group">
        <label for="project_description">Description:</label>
        <textarea id="project_description" name="project_description" maxlength="500"><?= $project["Description"] ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="project_tag">Tag*:</label>
        <input type="text" id="project_tag" name="project_tag" required value="<?= $project["Tag"] ?>">
    </div>
    
    <div class="form-group">
        <label>Coding Languages (comma separated):</label>
        <input type="text" id="coding_languages" name="coding_languages" placeholder="e.g., Python, JavaScript, C++" value="<?= $languages ?>">
    </div>
    
    <div class="form-group">
        <label>Experience Levels (comma separated, matching languages):</label>
        <input type="text" id="experience_levels" name="experience_levels" placeholder="e.g., Advanced, Intermediate, Beginner" value="<?= $experiences ?>">
    </div>
    
    <div class="form-actions">
        <button type="submit" name="edit_project" class="submit-btn">Edit Project</button>
        <button type="submit" name="delete_project" class="submit-btn" style="background-color: red;">Delete Project</button>

    </div>
</form>
</div>
</div>