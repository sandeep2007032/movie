<?php
require 'config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$movie_id = $_GET['id'];
$search_query = isset($_GET['query']) ? $_GET['query'] : ''; // Capture the search query parameter
$api_key = 'ed10107d'; // Replace with your OMDB API key
$response = file_get_contents("http://www.omdbapi.com/?i={$movie_id}&apikey={$api_key}");
$movie = json_decode($response, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $list_id = $_POST['list_id'];

    // Insert movie if not exists
    $stmt = $conn->prepare("INSERT INTO movies (omdb_id, title, year, poster) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
    $stmt->bind_param("ssss", $movie['imdbID'], $movie['Title'], $movie['Year'], $movie['Poster']);
    $stmt->execute();
    $movie_db_id = $stmt->insert_id;
    $stmt->close();

    // Add movie to list
    $stmt = $conn->prepare("INSERT INTO movie_list_items (list_id, movie_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $list_id, $movie_db_id);
    $stmt->execute();
    $stmt->close();

    echo "Movie added to list!";
}

// Fetch user's lists for the add to list form
$stmt = $conn->prepare("SELECT id, name FROM movie_lists WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$lists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['Title']) ?></title>
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
        img {
            max-width: 100%;
            height: auto;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 400px;
            margin: 20px auto;
        }
        select, button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            color: #fff;
            background-color: #007BFF;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
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
    <h1><?= htmlspecialchars($movie['Title']) ?> (<?= htmlspecialchars($movie['Year']) ?>)</h1>
    <img src="<?= htmlspecialchars($movie['Poster']) ?>" alt="<?= htmlspecialchars($movie['Title']) ?>">

    <form method="post" action="movie.php?id=<?= htmlspecialchars($movie_id) ?>&query=<?= urlencode($search_query) ?>">
        <select name="list_id" required>
            <?php foreach ($lists as $list): ?>
                <option value="<?= htmlspecialchars($list['id']) ?>"><?= htmlspecialchars($list['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add to List</button>
    </form>
</div>

<div class="footer">
    <p>&copy; 2024 Movie Library</p>
</div>

</body>
</html>
