<?php
session_start();
include('conekt.php');

// Redirect if user is not a leader
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: user_login.php");
    exit();
}

// Initialize filters
$gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
$street_filter = isset($_GET['street']) ? $_GET['street'] : 'Any';
$hobby_filter = isset($_GET['hobby']) ? $_GET['hobby'] : '';

// Handle upload request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $name = htmlspecialchars($_POST['name']);
    $age = (int)$_POST['age'];
    $description = htmlspecialchars($_POST['description']);
    $gender = htmlspecialchars($_POST['gender']);
    $street = htmlspecialchars($_POST['street']);
    $hobby = htmlspecialchars($_POST['hobby']);
    $birth_date = $_POST['birth_date'];
    $image_urls = [];

    // Handle multiple image uploads
    $files = $_FILES['images'];

    foreach ($files['tmp_name'] as $key => $tmp_name) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $fileTmpPath = $tmp_name;
            $fileName = $files['name'][$key];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExts)) {
                $uploadDir = 'uploads/';
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $image_urls[] = $newFileName;
                } else {
                    echo "<p>Error uploading file.</p>";
                    exit();
                }
            } else {
                echo "<p>Invalid file type.</p>";
                exit();
            }
        }
    }

    // Insert child details into the database
    $stmt = $conn_users->prepare("INSERT INTO children (name, age, description, gender, street, hobby, birth_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssss", $name, $age, $description, $gender, $street, $hobby, $birth_date);

    if ($stmt->execute()) {
        $child_id = $stmt->insert_id; // Get the last inserted child ID

        // Insert images into the child_images table
        foreach ($image_urls as $image_url) {
            $stmt_image = $conn_users->prepare("INSERT INTO child_images (child_id, image_url) VALUES (?, ?)");
            $stmt_image->bind_param("is", $child_id, $image_url);
            $stmt_image->execute();
            $stmt_image->close();
        }

        // Redirect after successful upload
        header("Location: leader_upload.php?success=1");
        exit();
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
    $conn_users->close();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Fetch images before deletion
    $image_stmt = $conn_users->prepare("SELECT image_url FROM child_images WHERE child_id = ?");
    $image_stmt->bind_param("i", $delete_id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();

    // Delete each image file from the server
    while ($image_row = $image_result->fetch_assoc()) {
        $file_path = 'uploads/' . $image_row['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path); // Delete file
        }
    }
    $image_stmt->close();

    // Delete child record and associated images from the database
    $stmt_delete = $conn_users->prepare("DELETE FROM children WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);
    $stmt_delete->execute();

    // Also delete child images from the database
    $stmt_delete_images = $conn_users->prepare("DELETE FROM child_images WHERE child_id = ?");
    $stmt_delete_images->bind_param("i", $delete_id);
    $stmt_delete_images->execute();

    $stmt_delete->close();
    $stmt_delete_images->close();
    $conn_users->close();

    header("Location: leader_upload.php?deleted=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Child</title>
    <link rel="stylesheet" href="z.css">
</head>
<body>
    <h1>Upload Child for Sponsorship</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="gender1">
                <p class="sponsor">I want to sponsor a:</p>
                <div class="gender3">
                    <label><input type="radio" name="gender" value="Boy" <?= $gender_filter === 'Boy' ? 'checked' : '' ?>> Boy</label>
                    <label><input type="radio" name="gender" value="Girl" <?= $gender_filter === 'Girl' ? 'checked' : '' ?>> Girl</label>
                    <br>
                    <label><input type="radio" name="gender" value="" <?= !$gender_filter ? 'checked' : '' ?>> Either</label>
                </div>
            </div>

            <div class="places">
                <p class="sponsor1">Who comes from:</p>
                <select name="street">
                    <option value="Any">All Streets</option>
                    <option value="lemara" <?= $street_filter === 'lemara' ? 'selected' : '' ?>>Lemara</option>
                    <option value="daraja_mbili" <?= $street_filter === 'daraja_mbili' ? 'selected' : '' ?>>Daraja Mbili</option>
                    <option value="sokoni_one" <?= $street_filter === 'sokoni_one' ? 'selected' : '' ?>>Sokoni One</option>
                    <option value="mjini_kati" <?= $street_filter === 'mjini_kati' ? 'selected' : '' ?>>Mjini Kati</option>
                    <option value="njiro" <?= $street_filter === 'njiro' ? 'selected' : '' ?>>Njiro</option>
                    <option value="kijenge" <?= $street_filter === 'kijenge' ? 'selected' : '' ?>>Kijenge</option>
                    <option value="moshono" <?= $street_filter === 'moshono' ? 'selected' : '' ?>>Moshono</option>
                    <option value="uzunguni" <?= $street_filter === 'uzunguni' ? 'selected' : '' ?>>Uzunguni</option>
                </select>
            </div>
        </div>

        <div>
            <input type="text" name="name" placeholder="Child's Name" required>
        </div>
        <div>
            <input type="number" name="age" placeholder="Child's Age" required>
        </div>
        <div>
            <textarea name="description" placeholder="Description" required></textarea>
        </div>
        <div>
            <select name="hobby" required>
                <option value="">Select Hobby</option>
                <option value="Music" <?= $hobby_filter === 'Music' ? 'selected' : '' ?>>Music</option>
                <option value="Dancing" <?= $hobby_filter === 'Dancing' ? 'selected' : '' ?>>Dancing</option>
                <option value="Cooking & Baking" <?= $hobby_filter === 'Cooking & Baking' ? 'selected' : '' ?>>Cooking & Baking</option>
                <option value="Swimming" <?= $hobby_filter === 'Swimming' ? 'selected' : '' ?>>Swimming</option>
                <option value="Drawing" <?= $hobby_filter === 'Drawing' ? 'selected' : '' ?>>Drawing</option>
                <option value="Sports" <?= $hobby_filter === 'Sports' ? 'selected' : '' ?>>Sports</option>
                <option value="Reading" <?= $hobby_filter === 'Reading' ? 'selected' : '' ?>>Reading</option>
                <option value="Gardening" <?= $hobby_filter === 'Gardening' ? 'selected' : '' ?>>Gardening</option>
                <option value="Computer" <?= $hobby_filter === 'Computer' ? 'selected' : '' ?>>Computer</option>
                <option value="Photography" <?= $hobby_filter === 'Photography' ? 'selected' : '' ?>>Photography</option>
                <option value="Writing" <?= $hobby_filter === 'Writing' ? 'selected' : '' ?>>Writing</option>
            </select>
        </div>
        <div>
            <input type="date" name="birth_date" required>
        </div>
        <div>
            <input type="file" name="images[]" multiple required>
        </div>
        <button type="submit" name="upload">Upload Child</button>
    </form>

    <h2>Uploaded Children</h2>
    <div>
        <?php
        $children_sql = "SELECT * FROM children";
        $children_result = $conn_users->query($children_sql);

        if ($children_result->num_rows > 0) {
            while ($child = $children_result->fetch_assoc()) {
                $child_id = $child['id'];
                echo '<div>';
                echo '<h3>' . htmlspecialchars($child['name']) . '</h3>';
                echo '<p>Age: ' . htmlspecialchars($child['age']) . '</p>';
                echo '<p>Description: ' . htmlspecialchars($child['description']) . '</p>';
                
                // Fetch images for this child
                $image_sql = "SELECT image_url FROM child_images WHERE child_id = ?";
                $stmt = $conn_users->prepare($image_sql);
                $stmt->bind_param('i', $child_id);
                $stmt->execute();
                $image_result = $stmt->get_result();
                
                if ($image_result->num_rows > 0) {
                    echo '<div class="child-images">';
                    while ($image_row = $image_result->fetch_assoc()) {
                        echo '<div class="child-image">';
                        echo '<img src="uploads/' . htmlspecialchars($image_row['image_url']) . '" alt="Child Image" width="100" height="100">'; // Display each child's image
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>No images available for this child.</p>';
                }

                echo '<a href="?delete_id=' . $child_id . '">Delete</a>';
                echo '</div><hr>'; // Divider between children
            }
        } else {
            echo '<p>No children uploaded yet.</p>';
        }
        ?>
    </div>

    <script>
        // Show success or deleted messages
        <?php if (isset($_GET['success'])): ?>
            alert('Child uploaded successfully!');
        <?php elseif (isset($_GET['deleted'])): ?>
            alert('Child deleted successfully!');
        <?php endif; ?>
    </script>
</body>
</html>
