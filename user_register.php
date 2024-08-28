<?php
session_start();
include('conekt.php'); // Database connection

// Predefined IDs for leaders
$allowed_leader_ids = ["Tz-0605-0178-2022", "Tz-0605-0178-2023", "Tz-0605-0178-2024"];

if (isset($_POST['register'])) {
    $id = $_POST['id'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $location = $_POST['location'];
    $p_no = $_POST['p_no'];
    $pass = $_POST['pass'];
    $roles = $_POST['roles'];
    $profile_picture = 'default.png'; // Default profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Define allowed file extensions and size limit
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
            $uploadFileDir = 'uploads/'; // Directory to save uploaded files
            $dest_path = $uploadFileDir . basename($fileName); // Avoid directory traversal
            
            // Check if directory exists and create if not
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true); // Create directory with correct permissions
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_picture = basename($fileName); // Use base name for file
            } else {
                echo "There was an error uploading the profile picture.";
                exit();
            }
        } else {
            echo "Invalid file type or size.";
            exit();
        }
    }

    // Validate ID based on role
    if ($roles == 'leader' && !in_array($id, $allowed_leader_ids)) {
        echo "<script>alert('Invalid leader ID. Please use a valid leader ID.');</script>";
        exit();
    } elseif ($roles == 'normal' && in_array($id, $allowed_leader_ids)) {
        echo "<script>alert('This ID is reserved for leaders. Please enter a different ID.');</script>";
        exit();
    }

    // Handle file upload (you would replace this comment with your file upload logic)
    // ...

    // Insert user into the database
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn_users->prepare("INSERT INTO users (id, fname, mname, lname, email, location, phone_number, password, profile_picture, roles) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $id, $fname, $mname, $lname, $email, $location, $p_no, $hashed_pass, $profile_picture, $roles);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!');</script>";
        echo "<footer><a href='user_login.php'>Login</a></footer>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn_users->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<link rel="stylesheet" href="e.css">
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <h1>User Registration</h1>
        <input type="text" name="id" placeholder="ID (e.g., Tz-0605-0178-2022)" required>
        <input type="text" name="fname" placeholder="First Name" required>
        <input type="text" name="mname" placeholder="Middle Name">
        <input type="text" name="lname" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="location" placeholder="Location" required>
        <input type="text" name="p_no" placeholder="Phone Number" required>
        <input type="password" name="pass" placeholder="Password" required>
        <select name="roles" required>
            <option value="leader">CDW's</option>
            <option value="normal">Participant</option>
        </select>
        <input type="file" name="profile_picture" accept="image/*">
        <button type="submit" name="register">Register</button>
    </form>
</body>
</html>
