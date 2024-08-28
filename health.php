<?php
include("conekt.php");

if (isset($_POST['upload'])) {
    $age = $_POST['age'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $letter = 'default.pdf'; // Default value, will be overwritten if a file is uploaded

    if (isset($_FILES['letter']) && $_FILES['letter']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['letter']['tmp_name'];
        $fileName = $_FILES['letter']['name'];
        $fileSize = $_FILES['letter']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions and size limit
        $allowedExtensions = ['pdf', 'txt'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
            $uploadFileDir = 'uploads/'; // Directory to save uploaded files
            $dest_path = $uploadFileDir . basename($fileName); // Avoid directory traversal

            // Check if directory exists and create if not
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true); // Create directory with correct permissions
            }

            // Move the uploaded file to the designated directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $letter = basename($fileName); // Use the base name of the file
            } else {
                echo "There was an error uploading the letter.";
                exit();
            }
        } else {
            echo "Invalid file type or size.";
            exit();
        }
    }

    // Sanitize the name to avoid SQL errors
    $name = mysqli_real_escape_string($conn_users, $name);

    if (empty($age) || empty($name) || empty($gender) || empty($letter)) {
        echo "Please fill in all fields.";
    } else {
        // Use prepared statements to avoid SQL injection and syntax errors
        $stmt = $conn_users->prepare("INSERT INTO health (`age`, `name`, `gender`, `letter`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $age, $name, $gender, $letter); // "isss" indicates types: integer, string, string, string

        if ($stmt->execute()) {
            echo "Successfully uploaded.";
        } else {
            echo "Error: " . $stmt->error; // More descriptive error message
        }
        $stmt->close(); // Close the statement
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Status Upload</title>
    <link rel="stylesheet" href="a.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="form-container">
        <h2>Health Status Page</h2>
        <form action="" method="post" enctype="multipart/form-data"> <!-- Added enctype for file upload -->
            <div class="input-field">
                <input type="number" name="age" placeholder="Age" required>
            </div>
            <div class="input-field">
                <label for="name">Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="input-field">
                <label for="gender">Gender</label>
                <select name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="input-field">
                <label for="letter">Upload Letter</label>
                <input type="file" name="letter" accept=".pdf, .txt" required> <!-- Ensure correct file type -->
            </div>
            <div class="button-container">
                <button type="submit" name="upload">Upload</button>
            </div>
        </form>
    </div>
</body>
</html>
