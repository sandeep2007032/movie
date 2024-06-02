<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $list_id = $_POST['id'];

        // Check if the list belongs to the user
        $stmt = $conn->prepare("SELECT id FROM movie_lists WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $list_id, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();

            // Delete list items associated with the list
            $stmt = $conn->prepare("DELETE FROM movie_list_items WHERE list_id = ?");
            $stmt->bind_param("i", $list_id);
            $stmt->execute();
            $stmt->close();

            // Now delete the list itself
            $stmt = $conn->prepare("DELETE FROM movie_lists WHERE id = ?");
            $stmt->bind_param("i", $list_id);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?message=List deleted successfully");
                exit;
            } else {
                $stmt->close();
                header("Location: index.php?error=Could not delete the list");
                exit;
            }
        } else {
            $stmt->close();
            header("Location: index.php?error=List not found or does not belong to you");
            exit;
        }
    } else {
        header("Location: index.php?error=Invalid list ID");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
