<?php
include 'navbar.php';

$host = 'db'; 
$dbname = 'Project-Finder'; 
$dbuser = 'root'; 
$dbpass = 'rooty'; 

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get projects from database
$sql = "SELECT p.PID, p.Name, p.Description, p.Tag, p.Capacity, p.Date_posted, u.NetID, u.name as creator_name 
        FROM PROJECT p 
        INNER JOIN users u ON p.NetID = u.NetID 
        ORDER BY p.Date_posted DESC";
$result = $conn->query($sql);

// Store projects in array
$listings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
}

// Current user's netID
$id = 'kas210009';

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
  </style>
</head>
<body>
  <div class="project-container">
    <h1>Available Projects</h1>
    
    <?php if (count($listings) > 0): ?>
      <?php foreach ($listings as $listing): ?>
        <div class="listing">
          <div class="listing-header">
            <div>
              <h2 class="listing-title"><?= htmlspecialchars($listing['Name']) ?></h2>
              <p class="listing-creator">Posted by: <?= htmlspecialchars($listing['creator_name']) ?> (<?= htmlspecialchars($listing['NetID']) ?>)</p>
            </div>
            <div class="tag"><?= htmlspecialchars($listing['Tag']) ?></div>
          </div>
          
          <p class="listing-description"><?= htmlspecialchars($listing['Description']) ?></p>
          
          <div class="listing-meta">
            <span class="meta-item"><strong>Capacity:</strong> <?= htmlspecialchars($listing['Capacity']) ?></span>
            <span class="meta-item"><strong>Date Listed:</strong> <?= date('F j, Y', strtotime($listing['Date_posted'])) ?></span>
          </div>
          
          <button class="join-button" onclick="handleJoin(<?= $listing['PID'] ?>)">Join Project</button>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-listings">
        <h2>No projects available at this time</h2>
        <p>Check back later or create your own project!</p>
      </div>
    <?php endif; ?>
  </div>

  <script>
    function handleJoin(id) {
      // You can replace this with an AJAX call to join the project
      fetch(`/api/join-project.php?project_id=${id}&user_id=<?= $id ?>`, {
        method: 'POST',
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('You have successfully joined this project!');
          // Optionally refresh the page or update the UI
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while trying to join the project.');
      });
    }
  </script>
</body>
</html>