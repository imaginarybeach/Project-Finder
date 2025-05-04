<?php
session_start();
include 'navbar.php';


// Start session if not already started
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

$host = 'db'; 
$dbname = 'Project_Finder'; 
$dbuser = 'root'; 
$dbpass = 'rooty'; 

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process join request if form is submitted
if (isset($_POST['join_project'])) {
    $project_id = $_POST['project_id'];
    
    // Use session NetID or fallback to hardcoded value
    $user_id = isset($_SESSION['NetID']) ? $_SESSION['NetID'] : 'kas210009';

    // Check project capacity
    $check_sql = "SELECT Capacity FROM PROJECT WHERE PID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $project_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    $capacity = $check_result["Capacity"];
    
    // Get list of users in project
    $check_sql = "SELECT * FROM WORKS_ON WHERE PID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $project_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $count = $check_result->num_rows;

    // Check if user is already in the project
    $check_sql = "SELECT * FROM WORKS_ON WHERE NetID = ? AND PID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $user_id, $project_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
      $join_message = "You are already a member of this project.";
      $join_status = "warning";
    } elseif ($count >= $capacity) {
        $join_message = "Project is already at maximum capacity.";
        $join_status = "warning";
    } else {
        // Add user to WORKS_ON table
        $insert_sql = "INSERT INTO WORKS_ON (NetID, PID) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("si", $user_id, $project_id);
        
        if ($insert_stmt->execute()) {
            $join_message = "You have successfully joined this project!";
            $join_status = "success";
        } else {
            $join_message = "Error: " . $insert_stmt->error;
            $join_status = "error";
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}

$tag_filter = "";
if (isset($_GET["filter_projects"])) {
  $tag_filter = $_GET["filter_value"];
}
$tag_like = "%" . $tag_filter . "%";

// Get projects from database
$sql = "SELECT PID, Name, Description, Tag, Capacity, Date_posted, NetID 
        FROM PROJECT WHERE Tag LIKE ?
        ORDER BY Date_posted DESC";
$filter_stmt = $conn->prepare($sql);
$filter_stmt->bind_param("s", $tag_like);
$filter_stmt->execute();
$result = $filter_stmt->get_result();

// Store projects in array
$listings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
}

// Current user's netID (from session or hardcoded)
$id = isset($_SESSION['NetID']) ? $_SESSION['NetID'] : 'kas210009';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Listings</title>
  <link rel="stylesheet" href="/css/style.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }
    .project-container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 0 15px;
    }
    .listing {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      padding: 25px;
      transition: transform 0.2s ease;
    }
    .listing:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .listing-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
    }
    .listing-title {
      font-size: 1.8em;
      margin: 0 0 8px 0;
      color: #333;
    }
    .listing-creator {
      font-size: 1em;
      color: #555;
      margin: 0;
    }
    .listing-description {
      font-size: 1.1em;
      color: #444;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    .listing-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
      font-size: 0.95em;
    }
    .meta-item {
      color: #666;
    }
    .meta-item strong {
      color: #444;
    }
    .tag {
      display: inline-block;
      background-color: #e0f2fe;
      color: #0369a1;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 0.85em;
      margin-bottom: 20px;
    }
    .join-button {
      padding: 10px 25px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1em;
      transition: background-color 0.2s;
    }
    .join-button:hover {
      background-color: #0056b3;
    }
    .no-listings {
      text-align: center;
      padding: 40px;
      color: #666;
    }
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 4px;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-warning {
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="project-container">
    <h1>Available Projects</h1>

    <form method="GET" action="">
      <input type="text" name="filter_value" placeholder="search tag" style=" padding: 10px 25px" value="<?= $tag_filter ?>">
      <button type="submit" name="filter_projects" class="join-button">Filter</button>
    </form>
    
    <?php if (isset($join_message)): ?>
      <div class="alert alert-<?= $join_status ?>">
        <?= $join_message ?>
      </div>
    <?php endif; ?>
    
    <?php if (count($listings) > 0): ?>
      <?php foreach ($listings as $listing): ?>
        <div class="listing">
          <div class="listing-header">
            <div>
              <h2 class="listing-title"><?= htmlspecialchars($listing['Name']) ?></h2>
              <p class="listing-creator">Posted by: <?= htmlspecialchars($listing['NetID']) ?></p>
            </div>
            <div class="tag"><?= htmlspecialchars($listing['Tag']) ?></div>
          </div>
          
          <p class="listing-description"><?= htmlspecialchars($listing['Description']) ?></p>
          
          <div class="listing-meta">
            <span class="meta-item"><strong>Capacity:</strong> <?= htmlspecialchars($listing['Capacity']) ?></span>
            <span class="meta-item"><strong>Date Listed:</strong> <?= date('F j, Y', strtotime($listing['Date_posted'])) ?></span>
          </div>
          
          <form method="POST" action="">
            <input type="hidden" name="project_id" value="<?= $listing['PID'] ?>">
            <button type="submit" name="join_project" class="join-button">Join Project</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-listings">
        <h2>No projects available at this time</h2>
        <p>Check back later or create your own project!</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>