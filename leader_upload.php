<?php
session_start();
include('conekt.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'leader') {
    header("Location: user_login.php");
    exit();
}

// Handle upload request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $description = $_POST['description'];
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
                    echo "Error uploading file.";
                    exit();
                }
            } else {
                echo "Invalid file type.";
                exit();
            }
        }
    }

    // Insert child details into the database
    $stmt = $conn_users->prepare("INSERT INTO children (name, age, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $age, $description); // Updated to "sis" for the correct parameter types

    if ($stmt->execute()) {
        $child_id = $stmt->insert_id; // Get the last inserted child ID

        // Insert images into the child_images table
        foreach ($image_urls as $image_url) {
            $stmt_image = $conn_users->prepare("INSERT INTO child_images (child_id, image_url) VALUES (?, ?)");
            $stmt_image->bind_param("is", $child_id, $image_url);
            $stmt_image->execute();
            $stmt_image->close();
        }

        // Redirect to the dashboard or a confirmation page after successful upload
        header("Location: leader_upload.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn_users->close();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

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
    <link rel="stylesheet" href=".css">
</head>
<body>
    <h1>Upload Child for Sponsorship</h1>
    <form action="" method="post" enctype="multipart/form-data">
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
            <input type="file" name="images[]" accept="image/*" multiple required>
        </div>
        <button type="submit" name="upload">Upload Child</button>
    </form>

    <h2>Uploaded Children</h2>
    <div>
        <?php
        // Fetch all uploaded children
        $child_query = "SELECT id, name, age FROM children";
        $child_result = $conn_users->query($child_query);

        if ($child_result->num_rows > 0) {
            while ($child = $child_result->fetch_assoc()) {
                echo '<div>';
                echo '<h3>' . htmlspecialchars($child['name']) . ' (Age: ' . htmlspecialchars($child['age']) . ')</h3>';
                echo '<a href="?delete_id=' . $child['id'] . '" onclick="return confirm(\'Are you sure you want to delete this child?\');">Delete</a>';
                echo '</div><hr>';
            }
        } else {
            echo '<p>No children uploaded yet.</p>';
        }
        ?>
    </div>
</body>
</html>
