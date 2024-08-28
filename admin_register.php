<?php
session_start();
include('conekt.php');

if (isset($_POST['register'])) {
    // Collect input data and sanitize
    $id = mysqli_real_escape_string($conn_admin, $_POST['id']);
    $fname = mysqli_real_escape_string($conn_admin, $_POST['fname']);
    $mname = mysqli_real_escape_string($conn_admin, $_POST['mname']);
    $lname = mysqli_real_escape_string($conn_admin, $_POST['lname']);
    $email = mysqli_real_escape_string($conn_admin, $_POST['email']);
    $pass = mysqli_real_escape_string($conn_admin, $_POST['pass']);
    
    // Handle file upload
    $profile_picture = 'default.png'; // Default placeholder

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Define allowed file extensions and size limit
        $allowedExtensions = ['jpg', 'jpeg', 'png','pdf'];
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

    // Validate input data
    if (empty($id) || empty($fname) || empty($lname) || empty($email) || empty($pass)) {
        echo "All fields should be filled.";
        exit();
    } 

    // Hash the password
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn_admin->prepare("INSERT INTO admin (id, fname, mname, lname, email, password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssss', $id, $fname, $mname, $lname, $email, $hashed_pass, $profile_picture);

    if ($stmt->execute()) {
        echo "Registration successful!";
        echo "<a href='admin_login.php'>Login</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn_admin->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Registration</h1>
        <form action="admin_register.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="text" name="id" id="id" placeholder="Tz-0605-0000-0000" required>
            </div>
            <div class="form-group">
                <label for="fname">First Name:</label>
                <input type="text" name="fname" id="fname" placeholder="First Name" required>
            </div>
            <div class="form-group">
                <label for="mname">Middle Name:</label>
                <input type="text" name="mname" id="mname" placeholder="Middle Name">
            </div>
            <div class="form-group">
                <label for="lname">Last Name:</label>
                <input type="text" name="lname" id="lname" placeholder="Last Name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="pass">Password:</label>
                <input type="password" name="pass" id="pass" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </div>
            <div class="form-group">
                <button type="submit" name="register">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
