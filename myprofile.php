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
    $dbname = "Project_Finder";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Handle form submission for creating a new project
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_project'])) {
        // Prepare and sanitize form data
        $projectName = $conn->real_escape_string($_POST['project_name']);
        $projectDescription = $conn->real_escape_string($_POST['project_description']);
        $projectTag = $conn->real_escape_string($_POST['project_tag']);
        $projectCapacity = intval($_POST['project_capacity']);
        $currentTimestamp = date("Y-m-d H:i:s"); // Current timestamp
        
        // First, get the next available PID
        $pidResult = $conn->query("SELECT MAX(PID) as max_pid FROM PROJECT");
        $pidRow = $pidResult->fetch_assoc();
        $newPID = isset($pidRow['max_pid']) ? $pidRow['max_pid'] + 1 : 1;
        
        // Insert the new project
        $insertStmt = $conn->prepare("INSERT INTO PROJECT (PID, Name, Description, Tag, Capacity, Date_posted, NetID) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("isssiss", $newPID, $projectName, $projectDescription, $projectTag, $projectCapacity, $currentTimestamp, $netID);
        
        if ($insertStmt->execute()) {
            // If coding languages were provided
            if (isset($_POST['coding_languages']) && !empty($_POST['coding_languages'])) {
                $languages = explode(",", $_POST['coding_languages']);
                $expLevels = explode(",", $_POST['experience_levels']);
                
                // Prepare statement for coding languages
                $langStmt = $conn->prepare("INSERT INTO PROJECT_CODING_LANGUAGES (PID, Language, Relative_experience) 
                                           VALUES (?, ?, ?)");
                
                for ($i = 0; $i < count($languages); $i++) {
                    $language = trim($conn->real_escape_string($languages[$i]));
                    $expLevel = isset($expLevels[$i]) ? trim($conn->real_escape_string($expLevels[$i])) : "Beginner";
                    
                    if (!empty($language)) {
                        $langStmt->bind_param("iss", $newPID, $language, $expLevel);
                        $langStmt->execute();
                    }
                }
                $langStmt->close();
            }
            
            // Add the creator as a member of the project
            $workStmt = $conn->prepare("INSERT INTO WORKS_ON (NetID, PID) VALUES (?, ?)");
            $workStmt->bind_param("si", $netID, $newPID);
            $workStmt->execute();
            $workStmt->close();
            
            $insertStmt->close();
            $success = "Project created successfully!";
        } else {
            $error = "Error creating project: " . $conn->error;
        }
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

    // Query to fetch projects the user is working on
    $projectsQuery = "SELECT p.PID, p.Name, p.Description, p.Tag, p.Capacity, p.Date_posted, 
                            (SELECT COUNT(*) FROM WORKS_ON w WHERE w.PID = p.PID) AS current_members,
                            (SELECT s.Name FROM STUDENT s WHERE s.NetID = p.NetID) AS creator_name,
                            (p.NetID = ?) AS is_creator
                     FROM PROJECT p 
                     JOIN WORKS_ON w ON p.PID = w.PID 
                     WHERE w.NetID = ?
                     ORDER BY p.Date_posted DESC";

                     
    
    $projectsStmt = $conn->prepare($projectsQuery);
    $projectsStmt->bind_param("ss", $netID, $netID);
    $projectsStmt->execute();
    $projectsResult = $projectsStmt->get_result();
    $userProjects = [];
    
    while ($project = $projectsResult->fetch_assoc()) {
        // Fetch coding languages for this project
        $languagesQuery = "SELECT Language, Relative_experience FROM PROJECT_CODING_LANGUAGES WHERE PID = ?";
        $languagesStmt = $conn->prepare($languagesQuery);
        $languagesStmt->bind_param("i", $project['PID']);
        $languagesStmt->execute();
        $languagesResult = $languagesStmt->get_result();
        
        $languages = [];
        while ($lang = $languagesResult->fetch_assoc()) {
            $languages[] = $lang;
        }
        
        $project['languages'] = $languages;
        
        $userProjects[] = $project;
        
        $languagesStmt->close();
    }
    
    $projectsStmt->close();

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
        .create-project {
            margin-top: 30px;
            text-align: left;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .create-project h2 {
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
        .my-projects h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .projects-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .project-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .project-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            position: relative;
        }
        .creator-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .project-body {
            padding: 15px;
        }
        .project-title {
            margin: 0;
            font-size: 20px;
        }
        .project-tag {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 10px;
        }
        .project-description {
            margin: 15px 0;
            color: #555;
            line-height: 1.5;
        }
        .project-meta {
            display: flex;
            justify-content: space-between;
            color: #777;
            font-size: 14px;
            margin-top: 15px;
        }
        .project-team {
            margin-top: 15px;
            font-weight: bold;
        }
        .project-languages {
            margin-top: 15px;
        }
        .language-pill {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .experience-tag {
            font-size: 10px;
            background-color: #6c757d;
            padding: 2px 5px;
            border-radius: 10px;
            margin-left: 3px;
        }
        .no-projects {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        @media (min-width: 768px) {
            .profile-container {
                max-width: 800px;
            }
            .projects-container {
                grid-template-columns: repeat(2, 1fr);
            }
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
        
        <div class="create-project">
            <h2>Create New Project</h2>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="project_name">Project Name*:</label>
                    <input type="text" id="project_name" name="project_name" required>
                </div>
                
                <div class="form-group">
                    <label for="project_description">Description:</label>
                    <textarea id="project_description" name="project_description" maxlength="500"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="project_tag">Tag*:</label>
                    <input type="text" id="project_tag" name="project_tag" required>
                </div>
                
                <div class="form-group">
                    <label for="project_capacity">Team Capacity:</label>
                    <input type="number" id="project_capacity" name="project_capacity" min="1" max="20" value="3">
                </div>
                
                <div class="form-group">
                    <label>Coding Languages (comma separated):</label>
                    <input type="text" id="coding_languages" name="coding_languages" placeholder="e.g., Python, JavaScript, C++">
                </div>
                
                <div class="form-group">
                    <label>Experience Levels (comma separated, matching languages):</label>
                    <input type="text" id="experience_levels" name="experience_levels" placeholder="e.g., Advanced, Intermediate, Beginner">
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="create_project" class="submit-btn">Create Project</button>
                </div>
            </form>
        </div>
        
        <!-- New Projects Section -->
        <div class="my-projects">
            <h2>My Projects</h2>
            
            <?php if(empty($userProjects)): ?>
                <div class="no-projects">
                    <p>You're not part of any projects yet. Create a new project or join existing ones!</p>
                </div>
            <?php else: ?>
                <div class="projects-container">
                    <?php foreach($userProjects as $project): ?>
                        <!-- pass project id to edit page -->
                        <form name="edit_<?= $project['PID'] ?>" action="/editproject.php" hidden> <input type="hidden" name="project_id" value="<?= $project['PID'] ?>"> </form>
                        <div class="project-card" onclick="document.forms['edit_<?= $project['PID'] ?>'].submit()">
                            <div class="project-header">
                                <h3 class="project-title"><?php echo htmlspecialchars($project['Name']); ?></h3>
                                <?php if($project['is_creator']): ?>
                                    <span class="creator-badge">Creator</span>
                                <?php endif; ?>
                                <div class="project-tag"><?php echo htmlspecialchars($project['Tag']); ?></div>
                            </div>
                            <div class="project-body">
                                <p class="project-description">
                                    <?php echo !empty($project['Description']) ? htmlspecialchars($project['Description']) : 'No description provided.'; ?>
                                </p>
                                
                                <div class="project-team">
                                    <span>Team: <?php echo $project['current_members']; ?>/<?php echo $project['Capacity']; ?> members</span>
                                </div>
                                
                                <?php if(!empty($project['languages'])): ?>
                                    <div class="project-languages">
                                        <?php foreach($project['languages'] as $lang): ?>
                                            <span class="language-pill">
                                                <?php echo htmlspecialchars($lang['Language']); ?>
                                                <span class="experience-tag"><?php echo htmlspecialchars($lang['Relative_experience']); ?></span>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="project-meta">
                                    <span>Created by: <?php echo htmlspecialchars($project['creator_name']); ?></span>
                                    <span>Posted: <?php echo date('M d, Y', strtotime($project['Date_posted'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // You could add JavaScript here to dynamically add/remove language fields
        // or to validate the form before submission
    </script>
</body>
</html>