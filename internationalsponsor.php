

<?php
session_start();
include('conekt.php');

// Check if the user is logged in and is a normal user (sponsor)
/*if (!isset($_SESSION['user_id']) || $_SESSION['roles'] !== 'normal') {
    header("Location: user_login.php");
    exit();
}*/

// Handle sponsorship form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id = $_POST['child_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];

    // Validate the sponsorship amount
    if ($amount < 5) {
        echo "<script>alert('Sponsorship amount must be at least $5.');</script>";
        exit();
    }

    // Insert sponsorship details into the database
    $sponsor_id = $_SESSION['user_id']; // Optional if you want to link sponsorship to a user
    $stmt = $conn_users->prepare("INSERT INTO sponsorships (child_id, sponsor_id, amount, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $child_id, $sponsor_id, $amount, $payment_method);

    if ($stmt->execute()) {
        echo "<script>alert('Sponsorship successful!');</script>";
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
    <title>Sponsorship - Choose a Child</title>
    <link rel="stylesheet" href="t.css">
</head>
<body>
    <h1>Choose a Child for Sponsorship</h1>

    <div>
        <?php
        // Fetch children available for sponsorship
        $sql = "SELECT id, name, age, description FROM children";
        $result = $conn_users->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div>';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<p>Age: ' . htmlspecialchars($row['age']) . '</p>';
                echo '<p>Description: ' . htmlspecialchars($row['description']) . '</p>';
                
                // Fetch images for this child
                $child_id = $row['id'];
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

                // Sponsorship form
                echo '<form action="" method="post">';
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
                echo '</div><hr>';
            }
        } else {
            echo '<p>No children available for sponsorship.</p>';
        }
        ?>
    </div>

</body>
</html>
