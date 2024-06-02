<?php
require 'config.php';

if (!isset($_GET['query'])) {
    header("Location: index.php");
    exit;
}

$query = urlencode($_GET['query']);
$api_key = 'ed10107d'; // Replace with your OMDB API key
$response = file_get_contents("http://www.omdbapi.com/?s={$query}&apikey={$api_key}");
$response_data = json_decode($response, true);

$movies = isset($response_data['Search']) ? $response_data['Search'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #333;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #f2f2f2;
            text-decoration: none;
            padding: 10px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: #333;
        }
        .container {
            flex: 1;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        li {
            margin: 10px 0;
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 600px;
        }
        li img {
            margin-right: 10px;
            width: 50px;
            height: auto;
        }
        li a {
            text-decoration: none;
            color: #007BFF;
            flex: 1;
        }
        li a:hover {
            text-decoration: underline;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php">Back to Home</a>
  
</div>

<div class="container">
    <h1>Search Results</h1>

    <?php if (!empty($movies)): ?>
        <ul>
            <?php foreach ($movies as $movie): ?>
                <li>
                    <img src="<?= htmlspecialchars($movie['Poster']) ?>" alt="<?= htmlspecialchars($movie['Title']) ?>">
                    <a href="movie.php?id=<?= htmlspecialchars($movie['imdbID']) ?>"><?= htmlspecialchars($movie['Title']) ?> (<?= htmlspecialchars($movie['Year']) ?>)</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No results found for your query.</p>
    <?php endif; ?>
</div>

<div class="footer">
    <p>&copy; 2024 Movie Library</p>
</div>

</body>
</html>
