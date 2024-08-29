<?php
// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conekt.php"); // Include your database connection file

if (isset($_POST['upload'])) {
    $age = $_POST['age'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $letter = ''; // Initialize letter variable

    // Process the uploaded file
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
                $letter = basename($fileName); // Use the base name of the file for database storage
            } else {
                echo "There was an error uploading the letter.";
                exit();
            }
        } else {
            echo "Invalid file type or size.";
            exit();
        }
    } else {
        echo "File upload error: " . $_FILES['letter']['error'];
        exit();
    }

    // Sanitize the name to avoid SQL errors
    $name = mysqli_real_escape_string($conn_users, $name);

    // Check if all fields are filled
    if (empty($age) || empty($name) || empty($gender) || empty($letter)) {
        echo "Please fill in all fields.";
    } else {
        // Use prepared statements to avoid SQL injection and syntax errors
        $stmt = $conn_users->prepare("INSERT INTO health (age, name, gender, letter) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $age, $name, $gender, $letter); // "isss" indicates types: integer, string, string, string

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "Successfully uploaded.";
        } else {
            echo "Error: " . $stmt->error; // More descriptive error message
        }
        $stmt->close(); // Close the statement
    }
}

// Function to download a file
function downloadFile($filePath) {
    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    } else {
        echo "File not found.";
    }
}

// Check if a download has been requested
if (isset($_GET['download'])) {
    $fileToDownload = basename($_GET['download']); // Sanitize the input
    $filePath = 'uploads/' . $fileToDownload; // Adjust the path based on your directory structure
    downloadFile($filePath);
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
                <label for="letter">Upload Filled Letter</label>
                <input type="file" name="letter" accept=".pdf, .txt" required> <!-- Ensure correct file type -->
            </div>
            <div class="button-container">
                <button type="submit" name="upload">Upload</button>
            </div>
        </form>
        
        <div class="download-link">
            <h3>Download Letter Template</h3>
            <a href="?download=health%20assessment%20form%20tz0605.pdf">Download Default Letter (PDF)</a>
            <br>
            <a href="?download=health%20assessment%20form%20tz0605.txt">Download Default Letter (TXT)</a>
        </div>
    </div>
</body>
</html>
