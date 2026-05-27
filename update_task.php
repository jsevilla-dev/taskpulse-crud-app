<?php
include 'db.php';

// Check if an ID was passed in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Secure it by converting to an integer

    // SQL statement to update the status
    $sql = "UPDATE tasks SET status = 'Completed' WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Send back to dashboard instantly
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating task: " . mysqli_error($conn);
    }
}
?>