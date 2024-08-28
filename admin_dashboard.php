<?php
session_start();
include('conekt.php');

// Ensure user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details from the database
$sql = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn_admin->prepare($sql);
$stmt->bind_param('s', $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    echo "Admin not found.";
    exit();
}

$stmt->close();
$conn_admin->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        h1 {
            text-align: center;
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info img {
            border-radius: 50%;
        }
        .profile-info p {
            font-size: 18px;
        }
        .links a {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .links a:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($admin['fname']); ?>!</h1>
        <div class="profile-info">
            <p><img src="uploads/<?php echo htmlspecialchars($admin['profile_picture']); ?>" alt="Profile Picture" width="150" height="150"></p>
            <p>ID: <?php echo htmlspecialchars($admin['id']); ?></p>
            <p>Name: <?php echo htmlspecialchars($admin['fname'] . " " . $admin['mname'] . " " . $admin['lname']); ?></p>
            <p>Email: <?php echo htmlspecialchars($admin['email']); ?></p>
        </div>
        <div class="links">
            <a href="manage_features.php">Manage Features</a>
            <a href="system_logs.php">View System Logs</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
