<?php
include 'navbar.php';

// dummy data for the LISTINGS (array)
$listings = [
    [
        'id' => 1,
        'title' => 'Javascript Project',
        'description' => 'A project about building web applications using React.',
    ],
    [
        'id' => 2,
        'title' => 'Python Project',
        'description' => 'A project focused on data analysis using Python.',
    ],
];

//dummy data for the LOGGED IN USER id
$id = 'kas210009';


?>


<link rel="stylesheet" href="/css/style.css">




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Listings</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .listing {
      width: 100%;
      padding: 20px;
      box-sizing: border-box;
      border-bottom: 1px solid #ccc;
    }
    .listing:nth-child(even) {
      background-color: #f9f9f9;
    }
    .listing-title {
      font-size: 1.5em;
      font-weight: bold;
    }
    .listing-description {
      font-size: 1em;
      color: #555;
    }
    .join-button {
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #007BFF;
      color: white;
      border: none;
      cursor: pointer;
    }
    .join-button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <?php foreach ($listings as $listing): ?>
    <div class="listing">
      <h2 class="listing-title"><?= htmlspecialchars($listing['title']) ?></h2>
      <p class="listing-description"><?= htmlspecialchars($listing['description']) ?></p>
      <button class="join-button" onclick="handleJoin(<?= $listing['id'] ?>)">Join</button>
    </div>
  <?php endforeach; ?>

  <script>
    function handleJoin(id) {
      alert(`Joining listing with ID ${id}`);
    }
  </script>
</body>
</html>
