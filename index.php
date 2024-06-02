<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's name
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Fetch user's movie lists
$stmt = $conn->prepare("SELECT id, name, is_public FROM movie_lists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lists = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();


?>
<?php if (isset($_GET['message'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_GET['message']) ?></p>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Library</title>
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
    overflow: hidden;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center; /* Center items vertically */
}

.navbar a {
    color: #f2f2f2;
    text-decoration: none;
    padding: 0 10px; /* Add padding to create space */
    transition: color 0.3s; /* Smooth transition for hover effect */
}

.navbar a:hover {
    color: #ddd; /* Change color on hover */
}


        .container {
            padding: 20px;
            text-align: center;
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a, .action-buttons form button {
            padding: 5px 10px;
            text-decoration: none;
            color: #007BFF;
            border: none;
            background: none;
            cursor: pointer;
        }
        .action-buttons a:hover, .action-buttons form button:hover {
            text-decoration: underline;
        }
        .action-buttons form {
            display: inline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="#">Welcome, <?= htmlspecialchars($username) ?></a>
    <div class="right">
        <a href="create_list.php">Create New List</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h1>Welcome to Movie Library</h1>

    <h2>Search Movies</h2>
    <form method="get" action="search.php">
        <input type="text" name="query" placeholder="Search for a movie" required>
        <button type="submit">Search</button>
    </form>

    <h2>Your Movie Lists</h2>
    <table>
        <thead>
            <tr>
                <th>List Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lists as $list): ?>
                <tr>
                    <td><a href="view_list.php?id=<?= $list['id'] ?>"><?= htmlspecialchars($list['name']) ?></a></td>
                    <td class="action-buttons">
                       
                        <form method="post" action="delete_list.php" onsubmit="return confirm('Are you sure you want to delete this list?');">
                            <input type="hidden" name="id" value="<?= $list['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
