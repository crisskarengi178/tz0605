<?php
session_start();
include('conekt.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $id = $_POST['id'];
    $pass = $_POST['pass'];

    if (empty($id) || empty($pass)) {
        header("Location: user_login.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn_users, $sql);
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['fname'] . ' ' . $row['lname'];
            $_SESSION['user_role'] = $row['roles'];
            $_SESSION['profile_picture'] = $row['profile_picture'];
            if ($row['roles'] === 'leader') {
                header("Location: cdws_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        $_SESSION['error'] = "No user found with this ID.";
    }

    // Redirect to avoid form resubmission
    header("Location: user_login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="e.css">
</head>
<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p class="error">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }
    ?>

    <form action="user_login.php" method="post">
        <h1>User Login</h1>
        <input type="text" name="id" placeholder="Username" required>
        <input type="password" name="pass" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
        <b>sign up</b>
        <a href="user_register.php">if you dont have account:<h1><u>register first</u></h1></a>
    </form>
</body>
</html>
