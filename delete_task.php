<?php
include 'db.php';

// Check if an ID was passed in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Secure it

    // SQL statement to delete the row
    $sql = "DELETE FROM tasks WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Send back to dashboard instantly
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting task: " . mysqli_error($conn);
    }
}
?>