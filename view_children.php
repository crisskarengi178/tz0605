<?php
session_start();
include('conekt.php');

// Ensure user is logged in and is a leader
/*if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'leader') {
    header("Location: user_login.php");
    exit();
}*/

// Fetch children data from the database
$sql = "SELECT id, name, age,  description FROM children";
$result = $conn_users->query($sql);
if(isset($_GET['id'])){
    $id=$_GET['id'];
    $result=mysqli_query($conn_users,"DELETE  from children");
    if($result){
        header("location:view_chidren.php");
    }else{
        echo"error".mysqli_error($conn_users);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sponsored Children</title>
    <link rel="stylesheet" href="o.css">
</head>
<body>
    <h1>Sponsored Children</h1>

    <div>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div>';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<p>Age: ' . htmlspecialchars($row['age']) . '</p>';
                //echo '<p>Amount: Ksh ' . htmlspecialchars(number_format($row['amount'], 2)) . '</p>';
                echo '<p>Description: ' . htmlspecialchars($row['description']) . '</p>';

                // Fetch images for this child
                $child_id = $row['id'];
                $image_sql = "SELECT image_url FROM child_images WHERE child_id = $child_id";
                $image_result = $conn_users->query($image_sql);

                if ($image_result->num_rows > 0) {
                    echo '<div class="image-gallery">';
                    while ($image_row = $image_result->fetch_assoc()) {
                        echo '<img src="uploads/' . htmlspecialchars($image_row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '" style="width:150px;height:150px;border-radius:10px;">';
                    }
                    echo '</div>';
                } else {
                    echo '<p>No images available.</p>';
                }

                echo '</div><hr>';
            }
        } else {
            echo '<p>No children available for sponsorship.</p>';
        }
        ?>
    </div>

</body>
</html>
