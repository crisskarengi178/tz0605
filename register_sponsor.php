<?php
session_start();
include('conekt.php');

// Handle sponsor registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $contacts = trim($_POST['contacts']);
    $email = trim($_POST['email']);
    $location = trim($_POST['location']);
    $job = trim($_POST['job']);
    $sponsorship_type = trim($_POST['sponsorship_type']);

    // Validate required fields
    if (empty($fullname) || empty($contacts) || empty($email) || empty($location) || empty($job) || empty($sponsorship_type)) {
        echo "All fields are required.";
    } else {
        // Prepare and bind
        $stmt = $conn_users->prepare("INSERT INTO sponsors (fullname, contacts, email, location, job, sponsorship_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $contacts, $email, $location, $job, $sponsorship_type);

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            // Get the newly created sponsor's ID
            $sponsor_id = $stmt->insert_id;

            // Redirect to the sponsorship page with the sponsor_id as a parameter
            header("Location: localsponsor.php?sponsor_id=" . $sponsor_id);
            exit();
        } else {
            echo "Error: " . $stmt->error; // Print error if there's an issue
        }

        $stmt->close();
    }

    $conn_users->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Registration</title>
    <link rel="stylesheet" href="r.css">
</head>
<body>
    <h1>Register to Sponsor a Child</h1>
    <form action="" method="post">
        <label for="fullname">Full Name:</label>
        <input type="text" name="fullname" required>
        
        <label for="contacts">Contact Number:</label>
        <input type="text" name="contacts" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>
        
        <label for="location">Location:</label>
        <input type="text" name="location" required>
        
        <label for="job">Job:</label>
        <input type="text" name="job" required>
        
        <label for="sponsorship_type">Sponsorship Type:</label>
        <select name="sponsorship_type" required>
            <option value="">Select Sponsorship Type</option>
            <option value="One-time">One-time</option>
            <option value="Monthly">Monthly</option>
        </select>

        <button type="submit">Register</button>
    </form>
</body>
</html>
