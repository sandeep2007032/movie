<?php
require 'config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$list_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch list details
$stmt = $conn->prepare("SELECT name, is_public, user_id FROM movie_lists WHERE id = ?");
$stmt->bind_param("i", $list_id);
$stmt->execute();
$stmt->bind_result($list_name, $is_public, $list_user_id);
$stmt->fetch();
$stmt->close();

// Check if the list is private and belongs to the logged-in user
if (!$is_public && $user_id != $list_user_id) {
    echo "This list is private.";
    exit;
}

// Fetch movies in the list
$stmt = $conn->prepare("SELECT m.id, m.omdb_id, m.title, m.year, m.poster FROM movies m 
                        JOIN movie_list_items li ON m.id = li.movie_id 
                        WHERE li.list_id = ?");
$stmt->bind_param("i", $list_id);
$stmt->execute();
$movies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Delete movie from list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'])) {
    $movie_id = $_POST['movie_id'];

    $delete_stmt = $conn->prepare("DELETE FROM movie_list_items WHERE list_id = ? AND movie_id = ?");
    $delete_stmt->bind_param("ii", $list_id, $movie_id);
    if ($delete_stmt->execute()) {
        // Movie successfully deleted from list
        header("Location: view_list.php?id=$list_id");
        exit;
    } else {
        echo "Error deleting movie from list.";
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie List: <?= htmlspecialchars($list_name) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 20px;
        }
        li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        li img {
            margin-right: 10px;
            max-width: 50px;
            height: auto;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }
        @media only screen and (max-width: 600px) {
            li {
                flex-direction: column;
                align-items: flex-start;
            }
            li img {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php">Back to Home</a>
</div>

<h1>Movie List: <?= htmlspecialchars($list_name) ?></h1>

<ul>
    <?php if (!empty($movies)): ?>
        <?php foreach ($movies as $movie): ?>
            <li>
                <img src="<?= $movie['poster'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <div>
                    <span><?= htmlspecialchars($movie['title']) ?> (<?= htmlspecialchars($movie['year']) ?>)</span>
                    <form method="post" action="">
                        <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                        <button type="submit">Remove</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No movies found in this list.</li>
    <?php endif; ?>
</ul>

</body>
</html>
