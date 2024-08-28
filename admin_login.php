<?php
session_start();
include('conekt.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['login'])) {
    // Collect and sanitize input data
    $email = mysqli_real_escape_string($conn_admin, $_POST['email']);
    $pass = mysqli_real_escape_string($conn_admin, $_POST['pass']);
    
    // Validate input data
    if (empty($email) || empty($pass)) {
        echo "Both email and password are required.";
    } else {
        // Prepare SQL query to fetch user data
        $stmt = $conn_admin->prepare("SELECT id, password FROM admin WHERE email = ?");
        
        if (!$stmt) {
            die("Prepare failed: " . $conn_admin->error);
        }
        
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        // Check if user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_pass);
            $stmt->fetch();
            
            // Verify the password
            if (password_verify($pass, $hashed_pass)) {
                // Set session variables
                $_SESSION['admin_id'] = $id;
                $_SESSION['email'] = $email;
                
                // Redirect to the dashboard
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No user found with this email.";
        }
        
        $stmt->close();
    }
}
$conn_admin->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
        .form-group input[type="email"],
        .form-group input[type="password"] {
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
        <h1>Admin Login</h1>
        <form action="admin_login.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="pass">Password:</label>
                <input type="password" name="pass" id="pass" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="login">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
