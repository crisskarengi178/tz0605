<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

include('conekt.php');

// Retrieve user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn_users, $sql);
mysqli_stmt_bind_param($stmt, 's', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_close($conn_users); // Always close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="f.css"> <!-- Link to external CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            display: flex;
            flex: 1;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            padding: 20px;
            color: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .sidebar img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px auto;
            border: 3px solid #0277bd;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar p {
            margin: 10px 0;
            font-size: 14px;
        }
        .nav {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }
        .nav a {
            color: white;
            padding: 12px;
            text-decoration: none;
            border-bottom: 1px solid #34495e;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }
        .nav a img.logo {
            width: 20px;
            height: auto;
            margin-right: 10px;
        }
        .nav a i {
            margin-right: 10px;
            font-size: 18px;
        }
        .nav a:hover {
            background-color: #1abc9c;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .profile-info {
            padding: 20px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }
        .profile-info h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #34495e;
        }
        .profile-info p {
            font-size: 16px;
            color: #555555;
        }
        .footer {
            background-color: #34495e;
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
            margin-top: auto;
        }

    </style>
</head>
<body>
    <div class="content">
        <div class="sidebar">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <h2><?php echo htmlspecialchars($user['fname']); ?></h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Location: <?php echo htmlspecialchars($user['location']); ?></p>
            <p>Phone Number: <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p>Participant: <?php echo htmlspecialchars($user['roles']); ?></p>
            <div class="nav">
                <h1>Dashboard</h1>
                <a href="sponsorship.php"><i class="fas fa-user"></i> Profile</a>
                <a href="document.php"><i class="fas fa-envelope"></i> Letter Mailing</a>
                <a href="health.php"><i class="fas fa-heartbeat"></i> Health Assessment</a>
                <a href="gift.php"><i class="fas fa-gift"></i> Gift Program</a>
                <a href="curri.php"><i class="fas fa-book"></i> Curriculum Sessions</a>
                <a href="user_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo htmlspecialchars($user['fname']); ?>!</h1>
            </div>
            <div class="profile-info">
                <h2>Yielding a child and a youth to be more of what they can become is our calling, by creating for them massive knowledge for a tremendous impact</h2>
                <p><b>Through our holistic child development model, we blend physical, social, economic, and spiritual care together to help children in poverty fully mature in every facet of life and transcend what is often a generational legacy of poverty.</b></p>
            </div>
            <img src="case.jpg" alt="" style="width: 100%; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        </div>
    </div>

    <footer class="footer">
        <h1>© 2024 Participants Dashboard. All rights reserved.</h1>
    </footer>
</body>
</html>
