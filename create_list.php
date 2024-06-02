<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO movie_lists (user_id, name, is_public) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $name, $is_public);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Movie List</title>
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
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            margin: 10px 0 5px;
            color: #333;
        }
        input[type="text"], input[type="checkbox"] {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
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
    <h1>Create New Movie List</h1>

    <form method="post" action="create_list.php">
        <label for="name">List Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="is_public">Public:</label>
        <input type="checkbox" id="is_public" name="is_public">
        
        <button type="submit">Create List</button>
    </form>
</div>

<div class="footer">
    <p>&copy; 2024 Movie Library</p>
</div>

</body>
</html>
