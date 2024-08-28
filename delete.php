<?php
include("conekt.php"); // Include your database connection file

// Check if the 'age' parameter is set in the URL
if (isset($_GET['age'])) {
    $age = $_GET['age'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn_users->prepare("DELETE FROM health WHERE age = ?");
    $stmt->bind_param("i", $age); // Assuming age is an integer

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        // If successful, redirect back to the health records page
        header("Location: view_health.php");
        exit();
    } else {
        // If there was an error, display an error message
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn_users->close();
?>
