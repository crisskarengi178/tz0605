<?php
session_start();
include('conekt.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

// Fetch children available for sponsorship
$sql = "SELECT id, name, age, description FROM children";
$result = $conn_users->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship - Choose a Child</title>
    <link rel="stylesheet" href="r.css">
</head>
<body>
    <h1>Choose a Child for Sponsorship</h1>

    <div>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $child_id = $row['id'];
                echo '<div>';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<p>Age: ' . htmlspecialchars($row['age']) . '</p>';
                echo '<p>Description: ' . htmlspecialchars($row['description']) . '</p>';
                
                // Fetch images for this child
                $image_sql = "SELECT image_url FROM child_images WHERE child_id = ?";
                $stmt = $conn_users->prepare($image_sql);
                $stmt->bind_param("i", $child_id);
                $stmt->execute();
                $image_result = $stmt->get_result();

                if ($image_result->num_rows > 0) {
                    echo '<div class="image-gallery">';
                    while ($image_row = $image_result->fetch_assoc()) {
                        echo '<img src="uploads/' . htmlspecialchars($image_row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '" style="width:150px;height:150px;border-radius:10px;">';
                    }
                    echo '</div>';
                } else {
                    echo '<p>No images available.</p>';
                }

                // Check if the user has already registered as a sponsor
                $sponsor_check_sql = "SELECT * FROM sponsors WHERE id = ?";
                $sponsor_stmt = $conn_users->prepare($sponsor_check_sql);
                $sponsor_stmt->bind_param("i", $_SESSION['user_id']);
                $sponsor_stmt->execute();
                $sponsor_result = $sponsor_stmt->get_result();

                if ($sponsor_result->num_rows > 0) {
                    // If the user is already registered, show the sponsorship form
                    echo '<form action="sponsorship_process.php" method="post">';
                    echo '<input type="hidden" name="child_id" value="' . $child_id . '">';
                    echo '<label for="amount">Sponsorship Amount (at least $5): </label>';
                    echo '<input type="number" name="amount" min="5" step="0.01" required>';
                    echo '<label for="payment_method">Payment Method:</label>';
                    echo '<select name="payment_method" required>';
                    echo '<option value="Credit Card">Credit Card</option>';
                    echo '<option value="PayPal">PayPal</option>';
                    echo '<option value="Bank Transfer">Bank Transfer</option>';
                    echo '</select>';
                    echo '<button type="submit">Sponsor This Child</button>';
                    echo '</form>';
                } else {
                    // If not registered, show link to register
                    echo '<a href="register_sponsor.php?child_id=' . $child_id . '">Sponsor This Child</a>';
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
