<?php
session_start();
include('conekt.php');

// Check if the user is logged in and is a leader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn_users->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDW Dashboard</title>
    <link rel="stylesheet" href="s.css">
    <link rel="stylesheet" href="t.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['fname']); ?>!</h1>
    
    <!-- Profile Picture Section -->
    <div class="profile-picture">
        <?php if (!empty($user['profile_picture']) && file_exists('uploads/profile_pictures/' . $user['profile_picture'])): ?>
            <img src="<?php echo 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="width:100px;height:100px;border-radius:50%;">
        <?php else: ?>
            <img src="path/to/default/profile/picture.png" alt="Default Profile Picture" style="width:100px;height:100px;border-radius:50%;">
        <?php endif; ?>
    </div>

    <nav>
        <ul>
            <li><a href="cdws_dashboard.php">Home</a></li>
            <li><a href="leader_upload.php">Upload Child for Sponsorship</a></li>
            <li><a href="view_children.php">View Sponsored Children</a></li>
            <li><a href="view_health.php">health assessment details</a></li>
            <li><a href="view_letter.php">view letter uploaded by sponsor</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <section>
        <h2>Dashboard Overview</h2>
        <p>This is your dashboard where you can manage tasks and view sponsorship details.</p>
    </section>
</body>
</html>
